@extends('layouts.crm')

@section('title', 'Недвижимость - Realt Bank')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/pages/properties/index/page-home.css') }}">

    <link rel="stylesheet" href="{{ versioned_asset('css/pages/properties/index/location-filter.css') }}">
@endpush

@section('header')
    {{-- Табы та title підтягуються автоматично з конфігу --}}
    <x-crm.header
            :addButton="true"
            addButtonText="Добавить"
            addButtonUrl="{{ route('properties.create') }}"
            addButtonPermission="properties.create"
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

    {{-- Модалка подтверждения удаления объекта --}}
    <div class="modal fade" id="deletePropertyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:320px;">
            <div class="modal-content" style="border-radius:12px;overflow:hidden;">
                <div class="modal-body p-0">
                    <div style="padding:16px 16px 12px;border-bottom:1px solid #eee;">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fw-bold" style="font-size:15px;">Удалить объект <span id="delete-property-id"></span>?</span>
                            <button type="button" class="btn-close" style="font-size:12px;" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div style="padding:12px 16px;">
                        <div class="d-flex gap-3 align-items-start">
                            <img id="delete-property-photo" src="" alt="" style="width:80px;height:60px;object-fit:cover;border-radius:6px;display:none;flex-shrink:0;">
                            <div style="font-size:13px;line-height:1.5;">
                                <div id="delete-property-deal-type" class="text-muted" style="display:none;"></div>
                                <div id="delete-property-type" style="display:none;"></div>
                                <div id="delete-property-address" class="text-muted" style="display:none;"></div>
                                <div id="delete-property-price" class="fw-bold" style="font-size:15px;margin-top:4px;"></div>
                            </div>
                        </div>
                    </div>
                    <div style="padding:12px 16px;border-top:1px solid #eee;" class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <form id="delete-property-form" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        window.canEditProperties = @json(auth()->user()->can('properties.edit'));
        window.canDeleteProperties = @json(auth()->user()->can('properties.delete'));
    </script>

    {{-- Модули таблицы (порядок важен!) --}}
    <script src="{{ versioned_asset('js/pages/properties/index/table-renderers.js') }}"></script>
    <script src="{{ versioned_asset('js/pages/properties/index/table-config.js') }}"></script>
    <script src="{{ versioned_asset('js/pages/properties/index/table-filters.js') }}"></script>
    <script src="{{ versioned_asset('js/pages/properties/index/location-filter.js') }}"></script>
    <script src="{{ versioned_asset('js/pages/properties/index/table-tags.js') }}"></script>
    <script src="{{ versioned_asset('js/pages/properties/index/page-home-table.js') }}"></script>

    {{-- Остальные скрипты --}}
    <script src="{{ versioned_asset('js/pages/filter1.min.js') }}"></script>
    <script src="{{ versioned_asset('js/pages/full-filter.js') }}"></script>
    <script src="{{ versioned_asset('js/pages/my-dropdown.min.js') }}"></script>
    <script src="{{ versioned_asset('js/pages/page-home.min.js') }}" type="module"></script>

    {{-- Удаление объекта из таблицы --}}
    <script>
        $(document).on('click', '.btn-delete-property', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var id = $btn.data('id');
            var photo = $btn.data('photo');
            var price = $btn.data('price');
            var dealType = $btn.data('deal-type');
            var propertyType = $btn.data('property-type');
            var address = $btn.data('address');

            $('#delete-property-id').text('#' + id);
            $('#delete-property-form').attr('action', '/properties/' + id);

            var $img = $('#delete-property-photo');
            if (photo) { $img.attr('src', photo).show(); } else { $img.hide(); }

            function showField(sel, val) {
                if (val && val !== '-') { $(sel).text(val).show(); } else { $(sel).hide(); }
            }

            showField('#delete-property-deal-type', dealType);
            showField('#delete-property-type', propertyType);
            showField('#delete-property-address', address);
            showField('#delete-property-price', price);

            new bootstrap.Modal(document.getElementById('deletePropertyModal')).show();
        });
    </script>

@endpush
