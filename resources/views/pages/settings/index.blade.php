@extends('layouts.crm')

@section('title', 'Настройки — FAKTOR CRM')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/settings/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/settings/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/settings/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/settings/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/settings/settings.css') }}">
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
            </div>
        </div>
    </div>

    {{-- Modals / Drawers --}}
    @include('pages.settings.roles.modals.drawer-role')
    @include('pages.settings.users.modals.drawer-user')
    @include('pages.settings.modals.confirm-delete')
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/settings/settings.js') }}"></script>
@endpush
