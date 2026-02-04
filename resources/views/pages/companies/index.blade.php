@extends('layouts.crm')

@section('title', 'Компании - Realt Bank')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/companies/index/page-company-company.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/companies/index/location-filter.css') }}">
@endpush

@section('header')
    <x-crm.header
            :addButton="true"
            title="Компания"
            addButtonText="Добавить"
            addButtonUrl="{{ route('companies.create') }}"
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
    @include('pages.companies.particles.index._filter')

    <!-- Таблица компаний -->
    <div class="table-responsive">
        @include('pages.companies.particles.index._table')
    </div>
@endsection

@push('scripts')
    {{-- Модули таблицы (порядок важен!) --}}
    <script src="{{ asset('js/pages/companies/index/table-renderers.js') }}"></script>
    <script src="{{ asset('js/pages/companies/index/table-config.js') }}"></script>
    <script src="{{ asset('js/pages/companies/index/table-filters.js') }}"></script>
    <script src="{{ asset('js/pages/companies/index/page-companies-table.js') }}"></script>
    <script src="{{ asset('js/pages/companies/index/location-filter.js') }}"></script>
@endpush
