@extends('layouts.crm')

@section('title', 'Недвижимость - Realt Bank')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/page-home.min.css') }}">

    <link rel="stylesheet" href="{{ asset('css/pages/properties/index/location-filter.css') }}">
@endpush

@section('header')
    {{-- Табы та title підтягуються автоматично з конфігу --}}
    <x-crm.header
            :addButton="true"
            addButtonText="Добавить"
            addButtonUrl="{{ route('properties.create') }}"
    />
@endsection

@section('content')
    {{-- Сообщения --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Фильтры -->
    <!-- початок filter	-->
    @include('pages/properties/particles/index/_filter')
    <!-- кінець filter	-->



    <!-- Таблица объектов -->
    <div class="table-responsive">
        @include('pages/properties/particles/index/_table')
    </div>

    {{-- Пагинация теперь рендерится через DataTables --}}
@endsection


@push('scripts')
    {{-- Модули таблицы (порядок важен!) --}}
    <script src="{{ asset('js/pages/properties/index/table-renderers.js') }}"></script>
    <script src="{{ asset('js/pages/properties/index/table-config.js') }}"></script>
    <script src="{{ asset('js/pages/properties/index/table-filters.js') }}"></script>
    <script src="{{ asset('js/pages/properties/index/location-filter.js') }}"></script>
    <script src="{{ asset('js/pages/properties/index/table-tags.js') }}"></script>
    <script src="{{ asset('js/pages/properties/index/page-home-table.js') }}"></script>

    {{-- Остальные скрипты --}}
    <script src="{{ asset('js/pages/filter1.min.js') }}"></script>
    <script src="{{ asset('js/pages/full-filter.js') }}"></script>
    <script src="{{ asset('js/pages/my-dropdown.min.js') }}"></script>
    <script src="{{ asset('js/pages/page-home.min.js') }}" type="module"></script>


@endpush
