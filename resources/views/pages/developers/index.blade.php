@extends('layouts.crm')

@section('title', 'Developers - Realt Bank')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/developers/page-developers.css') }}">

@endpush



@section('header')
    {{-- Табы та title підтягуються автоматично з конфігу --}}
    <x-crm.header
            :addButton="true"
            addButtonText="Добавить"
            addButtonUrl="{{ route('developers.create') }}"
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
    @include('pages/developers/particles/index/_filter')
    <!-- кінець filter	-->



    <!-- Таблица объектов -->
    <div class="table-responsive">
        @include('pages/developers/particles/index/_table')
    </div>

    {{-- Пагинация теперь рендерится через DataTables --}}
@endsection


@push('scripts')
    {{-- Модули таблицы (порядок важен!) --}}
    <script src="{{ asset('js/pages/developers/index/table-renderers.js') }}"></script>
    <script src="{{ asset('js/pages/developers/index/table-config.js') }}"></script>
    <script src="{{ asset('js/pages/developers/index/table-filters.js') }}"></script>
    <script src="{{ asset('js/pages/developers/index/page-developers-table.js') }}"></script>
@endpush
