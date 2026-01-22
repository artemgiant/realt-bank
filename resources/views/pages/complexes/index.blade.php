@extends('layouts.crm')

@section('title', 'Комплексы - Realt Bank')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/complexes/index/page-complex.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/complexes/index/location-filter.css') }}">
@endpush


@section('header')
    {{-- Табы та title підтягуються автоматично з конфігу --}}
    <x-crm.header
            :addButton="true"
            addButtonText="Добавить"
            addButtonUrl="{{ route('complexes.create') }}"
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

    {{-- Фильтры --}}
    @include('pages.complexes.particles.index._filter')

    {{-- Таблица --}}
    <div class="table-wrapper">
        @include('pages.complexes.particles.index._table')
    </div>

@endsection

@push('scripts')
    {{-- Location filter --}}
    <script src="{{ asset('js/pages/complexes/index/location-filter.js') }}"></script>

    {{-- Table modules --}}
    <script src="{{ asset('js/pages/complexes/index/table-renderers.js') }}"></script>
    <script src="{{ asset('js/pages/complexes/index/table-config.js') }}"></script>
    <script src="{{ asset('js/pages/complexes/index/table-filters.js') }}"></script>
    <script src="{{ asset('js/pages/complexes/index/table-tags.js') }}"></script>

    {{-- Main table initialization --}}
    <script src="{{ asset('js/pages/complexes/index/page-complex-table.js') }}"></script>
@endpush
