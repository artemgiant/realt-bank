@extends('layouts.crm')

@section('title', 'Недвижимость - Realt Bank')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/page-home.min.css') }}">
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
        <table  id="properties-table" class="table table-hover">
            <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Тип сделки</th>
                <th>Цена</th>
                <th>Площадь</th>
                <th>Статус</th>
                <th>Создано</th>
            </tr>
            </thead>
            <tbody>
            @forelse($properties as $property)
                <tr>
                    <td>{{ $property->id }}</td>
                    <td>
                        @php
                            $translation = $property->translations->firstWhere('locale', 'ru');
                            $title = $translation?->title ?: 'Без названия';
                        @endphp
                        <a href="{{ route('properties.show', $property) }}">
                            {{ Str::limit($title, 40) }}
                        </a>
                    </td>
                    <td>
                        @if($property->dealType)
                            <span class="badge bg-primary">{{ $property->dealType->name }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($property->price)
                            {{ number_format($property->price, 0, '.', ' ') }}
                            {{ $property->currency?->symbol ?? '$' }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($property->area_total)
                            {{ $property->area_total }} м²
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @switch($property->status)
                            @case('active')
                            <span class="badge bg-success">Активный</span>
                            @break
                            @case('draft')
                            <span class="badge bg-secondary">Черновик</span>
                            @break
                            @case('sold')
                            <span class="badge bg-info">Продано</span>
                            @break
                            @case('archived')
                            <span class="badge bg-dark">Архив</span>
                            @break
                            @default
                            <span class="badge bg-secondary">{{ $property->status }}</span>
                        @endswitch
                    </td>
                    <td>
                        {{ $property->created_at->format('d.m.Y') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="text-muted">
                            <p class="mb-2">Объекты не найдены</p>
                            <a href="{{ route('properties.create') }}" class="btn btn-primary">
                                Добавить первый объект
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Пагинация -->
    @if($properties->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $properties->links() }}
        </div>
    @endif
@endsection


@push('scripts')
    <script src="{{ asset('js/pages/page-home-table.min.js') }}"></script>
    <script src="{{ asset('js/pages/filter1.min.js') }}"></script>
    <script src="{{ asset('js/pages/full-filter.min.js') }}"></script>
    <script src="{{ asset('js/pages/my-dropdown.min.js') }}"></script>
    <script src="{{ asset('js/pages/page-home.min.js') }}" type="module"></script>
@endpush
