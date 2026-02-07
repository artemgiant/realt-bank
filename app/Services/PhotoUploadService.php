<?php

namespace App\Services;

use App\Models\Property\Property;
use App\Models\Property\PropertyPhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class PhotoUploadService
{
    /**
     * Конфигурация
     */
    protected array $config;

    /**
     * Диск хранения
     */
    protected string $disk;

    public function __construct()
    {
        $this->config = config('photos');
        $this->disk = $this->config['disk'] ?? 'public';
    }

    /**
     * Загрузить фотографии для объекта недвижимости
     *
     * @param Property $property
     * @param array $files Массив UploadedFile
     * @return array Массив созданных PropertyPhoto
     */
    public function uploadPhotos(Property $property, array $files): array
    {
        $uploadedPhotos = [];
        $currentCount = $property->photos()->count();
        $maxFiles = $this->config['upload']['max_files'] ?? 20;

        foreach ($files as $index => $file) {
            // Проверяем лимит
            if ($currentCount + $index >= $maxFiles) {
                break;
            }

            if (!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }

            try {
                $photo = $this->uploadSinglePhoto($property, $file, $currentCount + $index);
                if ($photo) {
                    $uploadedPhotos[] = $photo;
                }
            } catch (\Exception $e) {
                \Log::error('Ошибка загрузки фото: ' . $e->getMessage(), [
                    'property_id' => $property->id,
                    'file' => $file->getClientOriginalName(),
                ]);
                continue;
            }
        }

        return $uploadedPhotos;
    }

    /**
     * Загрузить одно фото
     */
    protected function uploadSinglePhoto(Property $property, UploadedFile $file, int $sortOrder): ?PropertyPhoto
    {
        // Генерируем имя файла
        $filename = $this->generateFilename($file);

        // Получаем пути
        $paths = $this->getPaths($property->id);

        // Обрабатываем изображение (конвертация HEIC, сохранение)
        $processedPath = $this->processAndSaveImage($file, $paths['originals'], $filename);

        if (!$processedPath) {
            return null;
        }

        // Создаем миниатюру
        $thumbnailPath = null;
        if ($this->config['thumbnails']['enabled'] ?? true) {
            $thumbnailPath = $this->createThumbnail($processedPath, $paths['thumbnails'], $filename);
        }

        // Определяем, будет ли это главное фото (первое)
        $isMain = $sortOrder === 0;

        // Создаем запись в БД
        return PropertyPhoto::create([
            'property_id' => $property->id,
            'path' => $processedPath,
            'filename' => $filename,
            'sort_order' => $sortOrder,
            'is_main' => $isMain,
        ]);
    }

    /**
     * Обработать и сохранить изображение
     */
    protected function processAndSaveImage(UploadedFile $file, string $directory, string $filename): ?string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        // Проверяем, нужна ли конвертация HEIC
        $needsConversion = in_array($extension, ['heic', 'heif']) &&
            ($this->config['processing']['convert_heic'] ?? true);

        // Создаем директорию если не существует
        Storage::disk($this->disk)->makeDirectory($directory);

        if ($needsConversion) {
            // Конвертируем HEIC в JPG через Intervention Image
            return $this->convertHeicAndSave($file, $directory, $filename);
        }

        // Обычное сохранение с оптимизацией качества
        return $this->saveImage($file, $directory, $filename);
    }

    /**
     * Сохранить изображение с оптимизацией
     */
    protected function saveImage(UploadedFile $file, string $directory, string $filename): ?string
    {
        try {
            $extension = strtolower($file->getClientOriginalExtension());

            // Если это не JPEG, сохраняем как есть
            if (!in_array($extension, ['jpg', 'jpeg'])) {
                $path = $directory . '/' . $filename . '.' . $extension;
                Storage::disk($this->disk)->put($path, file_get_contents($file->getRealPath()));
                return $path;
            }

            // Для JPEG применяем оптимизацию качества
            $quality = $this->config['processing']['quality'] ?? 85;
            $path = $directory . '/' . $filename . '.jpg';

            $image = Image::read($file->getRealPath());
            $encoded = $image->toJpeg($quality);

            Storage::disk($this->disk)->put($path, (string) $encoded);

            return $path;
        } catch (\Exception $e) {
            \Log::error('Ошибка сохранения изображения: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Конвертировать HEIC в JPG и сохранить
     */
    protected function convertHeicAndSave(UploadedFile $file, string $directory, string $filename): ?string
    {
        try {
            $quality = $this->config['processing']['quality'] ?? 85;
            $path = $directory . '/' . $filename . '.jpg';

            // Intervention Image с драйвером Imagick поддерживает HEIC
            $image = Image::read($file->getRealPath());
            $encoded = $image->toJpeg($quality);

            Storage::disk($this->disk)->put($path, (string) $encoded);

            return $path;
        } catch (\Exception $e) {
            \Log::error('Ошибка конвертации HEIC: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Создать миниатюру
     */
    protected function createThumbnail(string $originalPath, string $thumbnailDir, string $filename): ?string
    {
        try {
            $width = $this->config['thumbnails']['width'] ?? 300;
            $height = $this->config['thumbnails']['height'] ?? 200;
            $quality = $this->config['thumbnails']['quality'] ?? 80;
            $mode = $this->config['thumbnails']['mode'] ?? 'fit';

            // Создаем директорию
            Storage::disk($this->disk)->makeDirectory($thumbnailDir);

            // Читаем оригинал
            $fullPath = Storage::disk($this->disk)->path($originalPath);
            $image = Image::read($fullPath);

            // Применяем режим обрезки
            if ($mode === 'crop') {
                $image->cover($width, $height);
            } else {
                $image->scale($width, $height);
            }

            // Определяем расширение
            $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
            $thumbnailPath = $thumbnailDir . '/' . $filename . '.' . $extension;

            // Сохраняем
            if (in_array(strtolower($extension), ['jpg', 'jpeg'])) {
                $encoded = $image->toJpeg($quality);
            } elseif (strtolower($extension) === 'png') {
                $encoded = $image->toPng();
            } else {
                $encoded = $image->toJpeg($quality);
            }

            Storage::disk($this->disk)->put($thumbnailPath, (string) $encoded);

            return $thumbnailPath;
        } catch (\Exception $e) {
            \Log::error('Ошибка создания миниатюры: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Генерировать имя файла по формату из конфига
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $format = $this->config['filename_format'] ?? '{original}_{timestamp}';

        // Получаем оригинальное имя без расширения
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Очищаем имя от спецсимволов
        $originalName = Str::slug($originalName, '_');

        // Ограничиваем длину
        $originalName = Str::limit($originalName, 50, '');

        // Заменяем плейсхолдеры
        $filename = str_replace(
            ['{original}', '{timestamp}', '{random}'],
            [$originalName, time(), Str::random(8)],
            $format
        );

        return $filename;
    }

    /**
     * Получить пути для хранения фото объекта
     */
    protected function getPaths(int $propertyId): array
    {
        $base = $this->config['paths']['base'] ?? 'properties';
        $photos = $this->config['paths']['photos'] ?? 'photos';
        $originals = $this->config['paths']['originals'] ?? 'originals';
        $thumbnails = $this->config['paths']['thumbnails'] ?? 'thumbnails';

        $propertyPath = "{$base}/{$propertyId}/{$photos}";

        return [
            'base' => $propertyPath,
            'originals' => "{$propertyPath}/{$originals}",
            'thumbnails' => "{$propertyPath}/{$thumbnails}",
        ];
    }

    /**
     * Удалить фото
     */
    public function deletePhoto(PropertyPhoto $photo): bool
    {
        try {
            // Удаляем оригинал
            if ($photo->path && Storage::disk($this->disk)->exists($photo->path)) {
                Storage::disk($this->disk)->delete($photo->path);
            }

            // Удаляем миниатюру
            $thumbnailPath = $this->getThumbnailPath($photo->path);
            if ($thumbnailPath && Storage::disk($this->disk)->exists($thumbnailPath)) {
                Storage::disk($this->disk)->delete($thumbnailPath);
            }

            // Удаляем запись
            $photo->delete();

            return true;
        } catch (\Exception $e) {
            \Log::error('Ошибка удаления фото: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Получить путь миниатюры из пути оригинала
     */
    protected function getThumbnailPath(string $originalPath): ?string
    {
        $originals = $this->config['paths']['originals'] ?? 'originals';
        $thumbnails = $this->config['paths']['thumbnails'] ?? 'thumbnails';

        return str_replace("/{$originals}/", "/{$thumbnails}/", $originalPath);
    }

    /**
     * Удалить все фото объекта
     */
    public function deleteAllPhotos(Property $property): bool
    {
        try {
            // Получаем путь к папке объекта
            $paths = $this->getPaths($property->id);

            // Удаляем всю папку photos
            Storage::disk($this->disk)->deleteDirectory($paths['base']);

            // Удаляем записи из БД
            $property->photos()->delete();

            return true;
        } catch (\Exception $e) {
            \Log::error('Ошибка удаления всех фото: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Обновить порядок фото
     */
    public function updateOrder(Property $property, array $photoIds): bool
    {
        try {
            foreach ($photoIds as $order => $photoId) {
                PropertyPhoto::where('id', $photoId)
                    ->where('property_id', $property->id)
                    ->update([
                        'sort_order' => $order,
                        'is_main' => $order === 0,
                    ]);
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Ошибка обновления порядка фото: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Установить главное фото
     */
    public function setMainPhoto(Property $property, int $photoId): bool
    {
        try {
            // Снимаем is_main со всех фото
            $property->photos()->update(['is_main' => false]);

            // Устанавливаем новое главное фото
            PropertyPhoto::where('id', $photoId)
                ->where('property_id', $property->id)
                ->update(['is_main' => true]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Ошибка установки главного фото: ' . $e->getMessage());
            return false;
        }
    }
}
