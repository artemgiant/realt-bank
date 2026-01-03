<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Импорт комплексов</title>
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
                                <td>Улица (должна существовать в базе)</td>
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
                        Локации (страна, область, город, район, зона, улица) должны уже существовать в базе данных.
                    </div>
                </div>
            </div>

            {{-- Пример --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Пример данных</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class=
