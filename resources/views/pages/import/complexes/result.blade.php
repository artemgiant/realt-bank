<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Результат импорта</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Заголовок --}}
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('import.complexes.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h1 class="h3 mb-0">Результат импорта</h1>
            </div>

            {{-- Общая статистика --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <div class="h2 mb-0">{{ $result['total_rows'] }}</div>
                            <div class="text-muted">Всего строк</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body text-center">
                            <div class="h2 mb-0 text-success">
                                {{ $result['total_rows'] - count($result['errors']) }}
                            </div>
                            <div class="text-muted">Успешно</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-danger bg-opacity-10">
                        <div class="card-body text-center">
                            <div class="h2 mb-0 text-danger">{{ count($result['errors']) }}</div>
                            <div class="text-muted">Ошибок</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-warning bg-opacity-10">
                        <div class="card-body text-center">
                            <div class="h2 mb-0 text-warning">
                                {{ $result['skipped']['developers'] + $result['skipped']['complexes'] + $result['skipped']['blocks'] }}
                            </div>
                            <div class="text-muted">Пропущено (уже есть)</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Созданные объекты --}}
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-check-circle me-1"></i>
                        Создано объектов
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <div class="h3 mb-1 text-success">{{ $result['created']['developers'] }}</div>
                                <div class="text-muted">Застройщиков</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <div class="h3 mb-1 text-success">{{ $result['created']['complexes'] }}</div>
                                <div class="text-muted">Комплексов</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <div class="h3 mb-1 text-success">{{ $result['created']['blocks'] }}</div>
                                <div class="text-muted">Блоков</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Пропущенные объекты --}}
            @if ($result['skipped']['developers'] > 0 || $result['skipped']['complexes'] > 0 || $result['skipped']['blocks'] > 0)
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-skip-forward me-1"></i>
                            Пропущено (уже существуют)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="border rounded p-3">
                                    <div class="h3 mb-1 text-warning">{{ $result['skipped']['developers'] }}</div>
                                    <div class="text-muted">Застройщиков</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3">
                                    <div class="h3 mb-1 text-warning">{{ $result['skipped']['complexes'] }}</div>
                                    <div class="text-muted">Комплексов</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3">
                                    <div class="h3 mb-1 text-warning">{{ $result['skipped']['blocks'] }}</div>
                                    <div class="text-muted">Блоков</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Ошибки --}}
            @if (count($result['errors']) > 0)
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Ошибки ({{ count($result['errors']) }})
                        </h5>
                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#errorsCollapse">
                            Показать/Скрыть
                        </button>
                    </div>
                    <div class="collapse show" id="errorsCollapse">
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-sm table-striped mb-0">
                                    <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 100px;">Строка</th>
                                        <th>Ошибка</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($result['errors'] as $error)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ $error['row'] }}</span>
                                            </td>
                                            <td>{{ $error['message'] }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Действия --}}
            <div class="d-flex gap-2">
                <a href="{{ route('import.complexes.index') }}" class="btn btn-primary">
                    <i class="bi bi-upload me-1"></i>
                    Загрузить ещё файл
                </a>
            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
