<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Realt Bank'))</title>

    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">

    <!-- Бібліотеки CSS -->
    <link rel="stylesheet" href="{{ asset('css/lib/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lib/bootstrap.v5.3.3.min.css') }}">
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lib/data-range-picker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lib/fancybox.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css">

    <!-- Стилі сторінки -->
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <main class="wrapper">
        <!-- Sidebar -->
        <x-crm.sidebar />

        <!-- Основний контент -->
        <div class="container-fluid">
            <!-- Header -->
            @hasSection('header')
                @yield('header')
            @endif

            <!-- Контент сторінки -->
            @yield('content')
        </div>
    </main>

    <!-- Модальні вікна -->
    @stack('modals')

    <!-- Бібліотеки JS -->
    <script src="{{ asset('js/lib/popper.v2.11.8.min.js') }}"></script>
    <script src="{{ asset('js/lib/bootstrap.v5.3.3.min.js') }}"></script>
    <script src="{{ asset('js/lib/jquery.v3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/lib/data-tables.min.js') }}"></script>
    <script src="{{ asset('js/lib/moment.min.js') }}"></script>
    <script src="{{ asset('js/lib/data-range-picker.min.js') }}"></script>
    <script src="{{ asset('js/lib/select2.min.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-text-icon@1.0.0/dist/leaflet.text-icon.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script src="{{ asset('js/lib/fancybox.min.js') }}"></script>
    <script src="{{ asset('js/lib/heic2any.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <!-- Скрипти сторінки -->
    @stack('scripts')
</body>
</html>
