@extends('layouts.crm')

@section('title', 'Настройки - Realt Bank')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/pages/settings/base.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/pages/settings/layout.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/pages/settings/components.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/pages/settings/tables.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/pages/settings/locations.css') }}">
@endpush

@section('content')
    <div class="settings-page">
        <div class="page-header">
            <h1 class="page-title">Настройки</h1>
        </div>

        <div class="settings-layout">
            {{-- Left Navigation --}}
            @include('pages.settings.partials.nav')

            {{-- Right Content --}}
            <div class="settings-content">
                @include('pages.settings.users.section-users')
                @include('pages.settings.roles.section-roles')
                @include('pages.settings.permissions.section-permissions')

                {{-- Location sections --}}
                @include('pages.settings.locations.section-countries')
                @include('pages.settings.locations.section-regions')
                @include('pages.settings.locations.section-oblast-regions')
                @include('pages.settings.locations.section-cities')
                @include('pages.settings.locations.section-districts')
                @include('pages.settings.locations.section-zones')
                @include('pages.settings.locations.section-streets')
            </div>
        </div>
    </div>

    {{-- Modals / Drawers --}}
    @include('pages.settings.roles.modals.drawer-role')
    @include('pages.settings.users.modals.drawer-user')
    @include('pages.settings.modals.confirm-delete')

    {{-- Location Drawers --}}
    @include('pages.settings.locations.modals.drawer-country')
    @include('pages.settings.locations.modals.drawer-state')
    @include('pages.settings.locations.modals.drawer-region')
    @include('pages.settings.locations.modals.drawer-district')
    @include('pages.settings.locations.modals.drawer-city')
    @include('pages.settings.locations.modals.drawer-zone')
    @include('pages.settings.locations.modals.drawer-street')
@endsection

@push('scripts')
    <script>
        // Data for editing - must be declared before settings.js loads
        var rolesData = @json($roles->keyBy('id'));
        var usersData = @json($users->keyBy('id'));

        // Location data for editing
        var countriesData = @json(isset($countriesList) ? $countriesList->keyBy('id') : collect());
        var statesData = @json(isset($states) ? $states->keyBy('id') : collect());
        var citiesData = @json(isset($citiesList) ? $citiesList->keyBy('id') : collect());
        var regionsListData = @json(isset($regionsList) ? $regionsList->keyBy('id') : collect());
        var districtsData = @json(isset($districtsList) ? $districtsList->keyBy('id') : collect());
        var zonesData = @json(isset($zonesList) ? $zonesList->keyBy('id') : collect());
        var streetsData = @json(isset($streetsList) ? $streetsList->keyBy('id') : collect());
    </script>
    <script src="{{ versioned_asset('js/pages/settings/settings.js') }}"></script>
    <script src="{{ versioned_asset('js/pages/settings/settings-locations.js') }}"></script>

    {{-- Show flash messages --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast(@json(session('success')), 'success');
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast(@json(session('error')), 'error');
            });
        </script>
    @endif

    {{-- Show validation errors and re-open user drawer --}}
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var errorMessages = @json($errors->all());
                showToast(errorMessages.join('<br>'), 'error');

                @if(old('_method') === 'PUT' && old('email'))
                    {{-- Re-open drawer in edit mode with old input --}}
                    var oldAction = '{{ url()->previous() }}';
                    var userIdMatch = oldAction.match(/\/settings\/users\/(\d+)/);
                    if (!userIdMatch) {
                        {{-- Try to extract from old form action stored in request --}}
                        var formAction = document.getElementById('userForm');
                        if (formAction) {
                            {{-- Fallback: find user by email from old input --}}
                            var oldEmail = @json(old('email'));
                            for (var key in usersData) {
                                if (usersData[key].email === oldEmail) {
                                    openUserDrawer(parseInt(key));
                                    break;
                                }
                            }
                        }
                    } else {
                        openUserDrawer(parseInt(userIdMatch[1]));
                    }
                @elseif(old('name') && !old('_method'))
                    {{-- Re-open drawer in create mode --}}
                    openUserDrawer();
                @endif
            });
        </script>
    @endif
@endpush
