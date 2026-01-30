{{--
    Компонент для отображения сообщений об успехе, ошибке и валидации

    Использование:
    <x-alerts />

    Требуется подключить стили:
    <link rel="stylesheet" href="{{ asset('css/components/alerts.css') }}">
--}}

{{-- Сообщение об успехе --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Сообщение об ошибке --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Ошибки валидации --}}
@if($errors->any())
    <div class="form-validation-error">
        <div class="validation-error-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM11 7V13H13V7H11ZM11 15V17H13V15H11Z" fill="white"/>
            </svg>
        </div>
        <div class="validation-error-content">
            <strong class="validation-error-title">{{ $title ?? 'Пожалуйста, исправьте ошибки:' }}</strong>
            <ul class="validation-error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
