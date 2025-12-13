@extends('layouts.crm')

@section('title', 'Недвижимость - Realt Bank')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/page-home.min.css') }}">
@endpush

@section('header')
    <x-crm.header 
        title="Недвижимость"
        :tabs="[
            ['label' => 'Объекты', 'url' => route('properties.index'), 'active' => true],
            ['label' => 'Комплексы', 'url' => '#', 'active' => false],
            ['label' => 'Девелоперы', 'url' => '#', 'active' => false],
        ]"
        :addButton="true"
        addButtonText="Добавить"
        addButtonUrl="#"
    />
@endsection

@section('content')
    <!-- Фільтри -->
    <div class="filter">
        <div class="filter-header">
            <div class="my-dropdown">
                <div class="my-dropdown-input-wrapper">
                    <button class="my-dropdown-geo-btn" data-bs-toggle="modal" data-bs-target="#geoModal">
                        <picture>
                            <source srcset="{{ asset('img/icon/geo.svg') }}" type="image/webp">
                            <img src="{{ asset('img/icon/geo.svg') }}" alt="">
                        </picture>
                    </button>
                    
                    <label class="my-dropdown-label">
                        <input class="my-dropdown-input" type="text" autocomplete="off" placeholder="Введите название">
                    </label>
                    
                    <button class="my-dropdown-btn arrow-down" id="btn-open-menu" type="button">
                        <picture>
                            <source srcset="{{ asset('img/icon/arrow-right-white.svg') }}" type="image/webp">
                            <img src="{{ asset('img/icon/arrow-right-white.svg') }}" alt="">
                        </picture>
                    </button>
                </div>
                <div class="my-dropdown-list-wrapper" style="display: none">
                    <!-- Dropdown content буде тут -->
                </div>
            </div>
        </div>
    </div>

    <!-- Таблиця об'єктів -->
    <div class="table-responsive">
        <table id="properties-table" class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Тип</th>
                    <th>Ціна</th>
                    <th>Локація</th>
                    <th>Статус</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                {{-- Дані будуть завантажуватись через DataTables або Livewire --}}
            </tbody>
        </table>
    </div>
@endsection

@push('modals')
    <!-- Geo Modal -->
    <div class="modal fade" id="geoModal" tabindex="-1" aria-labelledby="geoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="geoModalLabel">Выбор локации</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="map" style="height: 400px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary">Применить</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/page-home-table.min.js') }}"></script>
    <script src="{{ asset('js/pages/filter1.min.js') }}"></script>
    <script src="{{ asset('js/pages/full-filter.min.js') }}"></script>
    <script src="{{ asset('js/pages/my-dropdown.min.js') }}"></script>
    <script src="{{ asset('js/pages/modal-geo.min.js') }}"></script>
    <script src="{{ asset('js/pages/page-home.min.js') }}" type="module"></script>
@endpush
