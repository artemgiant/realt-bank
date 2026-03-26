@extends('layouts.crm')

@section('title', 'Сотрудники - Realt Bank')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/pages/employees/index/page-employees.css') }}">
@endpush

@section('header')
    <x-crm.header
        :addButton="false"
        title="Сотрудники"
    />
@endsection

@section('content')
    {{-- Фильтры --}}
    @include('pages.employees.particles.index._filter')

    {{-- Таблица сотрудников --}}
    <div class="table-responsive">
        @include('pages.employees.particles.index._table')
    </div>
@endsection


@push('modals')
    @include('pages.employees.modals._add-employee')
    @include('pages.employees.modals._delete-employee')
@endpush

@push('scripts')
    {{-- Shared URL filter sync utility --}}
    <script src="{{ versioned_asset('js/lib/url-filter-sync.js') }}"></script>

    {{-- Модули таблицы (порядок важен!) --}}
    <script src="{{ versioned_asset('js/pages/employees/index/table-config.js') }}"></script>
    <script src="{{ versioned_asset('js/pages/employees/index/table-renderers.js') }}"></script>
    <script src="{{ versioned_asset('js/pages/employees/index/table-filters.js') }}"></script>
    <script src="{{ versioned_asset('js/pages/employees/index/page-employees-table.js') }}"></script>

    {{-- Общие утилиты --}}
    <script src="{{ versioned_asset('js/pages/filter2.js') }}"></script>
    {{-- PhoneInputManager для модалки добавления сотрудника --}}
    <script src="{{ versioned_asset('js/pages/function_on_pages-create.js') }}"></script>
    <script src="{{ versioned_asset('js/pages/add-employee-modal.js') }}"></script>
@endpush
