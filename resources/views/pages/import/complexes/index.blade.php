<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Импорт комплексов - Realt Bank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Заголовок --}}
            <div class="d-flex align-items-center mb-4">
                <h1 class="h3 mb-0">Импорт комплексов</h1>
            </div>

            {{-- Сообщение об очистке --}}
            @if (session('cleared'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-1"></i>
                    Данные очищены{{ session('cleared.source') ? ' (source: ' . session('cleared.source') . ')' : '' }}:
                    блоков — {{ session('cleared.blocks') }},
                    комплексов — {{ session('cleared.complexes') }},
                    застройщиков — {{ session('cleared.developers') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Текущие данные и очистка --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Текущие данные</h5>
                    @if ($counts['developers'] + $counts['complexes'] + $counts['blocks'] > 0)
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#clearModal">
                            <i class="bi bi-trash me-1"></i>Очистить все
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="fs-3 fw-bold">{{ $counts['developers'] }}</div>
                            <div class="text-muted">Застройщиков</div>
                        </div>
                        <div class="col-4">
                            <div class="fs-3 fw-bold">{{ $counts['complexes'] }}</div>
                            <div class="text-muted">Комплексов</div>
                        </div>
                        <div class="col-4">
                            <div class="fs-3 fw-bold">{{ $counts['blocks'] }}</div>
                            <div class="text-muted">Блоков</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Модалка подтверждения очистки --}}
            <div class="modal fade" id="clearModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('import.complexes.clear') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-header">
                                <h5 class="modal-title">Подтверждение очистки</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Источник (source)</label>
                                    <select name="source" class="form-select" id="clearSource">
                                        <option value="">Все источники</option>
                                        @foreach ($sources as $source)
                                            <option value="{{ $source }}">{{ $source }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Выберите конкретный source или оставьте пустым для удаления всех</div>
                                </div>
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Это действие необратимо!
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-1"></i>Очистить
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Форма загрузки --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Загрузка Excel файла</h5>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('import.complexes.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="file" class="form-label">Выберите файл (xlsx, xls)</label>
                            <input type="file"
                                   class="form-control @error('file') is-invalid @enderror"
                                   id="file"
                                   name="file"
                                   accept=".xlsx,.xls"
                                   required>
                            <div class="form-text">Максимальный размер файла: 10MB</div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-1"></i>
                            Загрузить и импортировать
                        </button>
                    </form>
                </div>
            </div>

            {{-- Инструкция по формату --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Формат файла</h5>
                </div>
                <div class="card-body">
                    <p>Excel файл должен содержать следующие колонки:</p>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                            <tr>
                                <th>Колонка</th>
                                <th>Обязательно</th>
                                <th>Описание</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><code>developer</code></td>
                                <td><span class="badge bg-danger">Да</span></td>
                                <td>Название застройщика (создаётся если не существует)</td>
                            </tr>
                            <tr>
                                <td><code>complex</code></td>
                                <td><span class="badge bg-danger">Да</span></td>
                                <td>Название комплекса (создаётся если не существует)</td>
                            </tr>
                            <tr>
                                <td><code>block</code></td>
                                <td><span class="badge bg-danger">Да</span></td>
                                <td>Название блока/секции (создаётся если не существует)</td>
                            </tr>
                            <tr>
                                <td><code>country</code></td>
                                <td><span class="badge bg-secondary">Нет</span></td>
                                <td>Страна (должна существовать в базе)</td>
                            </tr>
                            <tr>
                                <td><code>state</code></td>
                                <td><span class="badge bg-secondary">Нет</span></td>
                                <td>Область (должна существовать в базе)</td>
                            </tr>
                            <tr>
                                <td><code>city</code></td>
                                <td><span class="badge bg-danger">Да</span></td>
                                <td>Город (должен существовать в базе)</td>
                            </tr>
                            <tr>
                                <td><code>district</code></td>
                                <td><span class="badge bg-secondary">Нет</span></td>
                                <td>Район (должен существовать в базе)</td>
                            </tr>
                            <tr>
                                <td><code>zone</code></td>
                                <td><span class="badge bg-secondary">Нет</span></td>
                                <td>Зона/микрорайон (должна существовать в базе)</td>
                            </tr>
                            <tr>
                                <td><code>street</code></td>
                                <td><span class="badge bg-secondary">Нет</span></td>
                                <td>Улица (создаётся если не существует)</td>
                            </tr>
                            <tr>
                                <td><code>house</code></td>
                                <td><span class="badge bg-secondary">Нет</span></td>
                                <td>Номер дома</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Важно:</strong> Первая строка файла должна содержать заголовки колонок.
                        Локации (страна, область, город, район, зона) должны уже существовать в базе данных.
                        Улицы создаются автоматически если не найдены.
                    </div>
                </div>
            </div>

            {{-- Пример данных --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Пример данных</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                            <tr>
                                <th>developer</th>
                                <th>complex</th>
                                <th>block</th>
                                <th>country</th>
                                <th>state</th>
                                <th>city</th>
                                <th>district</th>
                                <th>zone</th>
                                <th>street</th>
                                <th>house</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Kadorr Group</td>
                                <td>Kadorr City</td>
                                <td>Секция 1</td>
                                <td>Украина</td>
                                <td>Одесская</td>
                                <td>Одесса</td>
                                <td>Приморский</td>
                                <td>Аркадия</td>
                                <td>Генуэзская</td>
                                <td>24</td>
                            </tr>
                            <tr>
                                <td>Kadorr Group</td>
                                <td>Kadorr City</td>
                                <td>Секция 2</td>
                                <td>Украина</td>
                                <td>Одесская</td>
                                <td>Одесса</td>
                                <td>Приморский</td>
                                <td>Аркадия</td>
                                <td>Генуэзская</td>
                                <td>24А</td>
                            </tr>
                            <tr>
                                <td>Будова</td>
                                <td>Море Парк</td>
                                <td>Литер А</td>
                                <td>Украина</td>
                                <td>Одесская</td>
                                <td>Одесса</td>
                                <td>Киевский</td>
                                <td></td>
                                <td>Люстдорфская дорога</td>
                                <td>100</td>
                            </tr>
                            <tr>
                                <td>Гефест</td>
                                <td>Гефест</td>
                                <td>Корпус 1</td>
                                <td></td>
                                <td></td>
                                <td>Одесса</td>
                                <td>Пересыпский (Суворовский)</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Обратите внимание:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Пустые ячейки допускаются для необязательных полей</li>
                            <li>Район можно указывать с альтернативным названием в скобках: <code>Пересыпский (Суворовский)</code></li>
                            <li>Регистр букв не важен для поиска локаций</li>
                            <li>Застройщик, комплекс и блок создаются автоматически если не существуют</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
