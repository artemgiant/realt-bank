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
    {{-- JS будет добавлен позже --}}
@endpush
