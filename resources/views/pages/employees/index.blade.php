@extends('layouts.crm')

@section('title', 'Команда - Realt Bank')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/employees/index/page-employees.css') }}">
@endpush

@section('header')

    <x-crm.header
            :addButton="true"
            title="Компании"
            addButtonText="Добавить"

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
@endpush

@push('scripts')
    {{-- Модули таблицы (порядок важен!) --}}
    <script src="{{ asset('js/pages/employees/index/table-config.js') }}"></script>
    <script src="{{ asset('js/pages/employees/index/table-renderers.js') }}"></script>
    <script src="{{ asset('js/pages/employees/index/table-filters.js') }}"></script>
    <script src="{{ asset('js/pages/employees/index/page-employees-table.js') }}"></script>

    {{-- Общие утилиты --}}
    <script src="{{ asset('js/pages/filter2.js') }}"></script>
    <script src="{{ asset('js/pages/add-employee-modal.js') }}"></script>
@endpush
