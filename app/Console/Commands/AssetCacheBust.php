<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AssetCacheBust extends Command
{
    protected $signature = 'assets:bust';

    protected $description = 'Генерирует новый хеш для сброса кеша CSS/JS и очищает кеш конфига';

    public function handle(): int
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->error('.env файл не найден');
            return self::FAILURE;
        }

        $hash = substr(md5(Str::random(16) . microtime()), 0, 5);
        $envContent = file_get_contents($envPath);

        if (str_contains($envContent, 'ASSET_VERSION=')) {
            $envContent = preg_replace('/^ASSET_VERSION=.*$/m', 'ASSET_VERSION=' . $hash, $envContent);
        } else {
            $envContent .= "\nASSET_VERSION=" . $hash . "\n";
        }

        file_put_contents($envPath, $envContent);

        $this->call('config:clear');

        $this->info("ASSET_VERSION обновлён: {$hash}");

        return self::SUCCESS;
    }
}
