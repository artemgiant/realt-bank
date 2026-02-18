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
            <!-- LEFT NAV -->
            <div class="settings-nav">
                <div class="nav-section-label">Доступ</div>
                <div class="nav-group">
                    <div class="nav-item active" onclick="showSection('users')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                        Пользователи
                        <span class="nav-item-badge">101</span>
                    </div>
                    <div class="nav-item" onclick="showSection('roles')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Роли
                        <span class="nav-item-badge">8</span>
                    </div>
                    <div class="nav-item" onclick="showSection('permissions')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        Разрешения
                    </div>
                </div>

            </div>

            <!-- RIGHT CONTENT -->
            <div class="settings-content">

                <!-- ===== SECTION: USERS ===== -->
                <div class="settings-section active" id="section-users">
                    <div class="settings-breadcrumb">
                        <a href="#">Настройки</a>
                        <span>›</span>
                        <span class="current">Пользователи</span>
                    </div>

                    <div class="section-header">
                        <div>
                            <h2>Пользователи</h2>
                            <p>Управление пользователями системы</p>
                        </div>
                        <div style="display:flex;gap:12px;align-items:center;">
                            <div class="section-search">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                <input placeholder="Поиск пользователя...">
                            </div>
                            <button class="btn btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Добавить
                            </button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body" style="padding:0;">
                            <table class="roles-table">
                                <thead>
                                <tr>
                                    <th>Пользователь</th>
                                    <th>Сотрудник</th>
                                    <th>Email</th>
                                    <th>Роль</th>
                                    <th>Офис</th>
                                    <th>Статус</th>
                                    <th style="width:100px">Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><span class="role-name">Владимир Волков</span></td>
                                    <td style="font-size:13px">Волков Владимир Александрович</td>
                                    <td style="color:var(--text-muted);font-size:13px">v.volkov@realtbank.com.ua</td>
                                    <td><span class="role-badge admin">Super Admin</span></td>
                                    <td style="font-size:13px">Главный офис</td>
                                    <td><span class="tag tag-primary">Активен</span></td>
                                    <td>
                                        <div class="actions-cell">
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg></button>
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="role-name">Анна Коваленко</span></td>
                                    <td style="font-size:13px">Коваленко Анна Игоревна</td>
                                    <td style="color:var(--text-muted);font-size:13px">a.kovalenko@realtbank.com.ua</td>
                                    <td><span class="role-badge manager">Директор</span></td>
                                    <td style="font-size:13px">Главный офис</td>
                                    <td><span class="tag tag-primary">Активен</span></td>
                                    <td>
                                        <div class="actions-cell">
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg></button>
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="role-name">Дмитрий Петренко</span></td>
                                    <td style="font-size:13px">Петренко Дмитрий Олегович</td>
                                    <td style="color:var(--text-muted);font-size:13px">d.petrenko@realtbank.com.ua</td>
                                    <td><span class="role-badge manager">Руководитель офиса</span></td>
                                    <td style="font-size:13px">Офис Центр</td>
                                    <td><span class="tag tag-primary">Активен</span></td>
                                    <td>
                                        <div class="actions-cell">
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg></button>
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="role-name">Елена Шевченко</span></td>
                                    <td style="font-size:13px">Шевченко Елена Викторовна</td>
                                    <td style="color:var(--text-muted);font-size:13px">e.shevchenko@realtbank.com.ua</td>
                                    <td><span class="role-badge agent">Агент</span></td>
                                    <td style="font-size:13px">Офис Центр</td>
                                    <td><span class="tag tag-primary">Активен</span></td>
                                    <td>
                                        <div class="actions-cell">
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg></button>
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="role-name">Игорь Бондаренко</span></td>
                                    <td style="font-size:13px">Бондаренко Игорь Сергеевич</td>
                                    <td style="color:var(--text-muted);font-size:13px">i.bondarenko@realtbank.com.ua</td>
                                    <td><span class="role-badge agent">Агент</span></td>
                                    <td style="font-size:13px">Офис Приморский</td>
                                    <td><span class="tag">Неактивен</span></td>
                                    <td>
                                        <div class="actions-cell">
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg></button>
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ===== SECTION: ROLES ===== -->
                <div class="settings-section" id="section-roles">
                    <div class="settings-breadcrumb">
                        <a href="#">Настройки</a> <span>›</span> <span class="current">Роли</span>
                    </div>
                    <div class="section-header">
                        <div>
                            <h2>Роли пользователей</h2>
                            <p>Управляйте ролями и назначайте уровни доступа</p>
                        </div>
                        <button class="btn btn-primary" onclick="openDrawer()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Добавить роль
                        </button>
                    </div>

                    <div class="card">
                        <div class="card-body" style="padding:0;">
                            <table class="roles-table">
                                <thead>
                                <tr>
                                    <th>Роль</th>
                                    <th>Тип</th>
                                    <th>Пользователей</th>
                                    <th>Описание</th>
                                    <th style="width:100px">Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><span class="role-name">Super Admin</span></td>
                                    <td><span class="role-badge admin">Администратор</span></td>
                                    <td>1</td>
                                    <td style="color:var(--text-muted);font-size:13px">Абсолютный контроль над всеми разделами, правами, офисами, статистикой</td>
                                    <td>
                                        <div class="actions-cell">
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg></button>
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="role-name">System Admin</span></td>
                                    <td><span class="role-badge admin">Администратор</span></td>
                                    <td>2</td>
                                    <td style="color:var(--text-muted);font-size:13px">Техническая часть CRM: роли, настройка полей, API, интеграции</td>
                                    <td>
                                        <div class="actions-cell">
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg></button>
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="role-name">Agency Director</span></td>
                                    <td><span class="role-badge manager">Руководитель</span></td>
                                    <td>1</td>
                                    <td style="color:var(--text-muted);font-size:13px">Стратегия, финансы, структура офисов. Полный обзор всех офисов и отчётов</td>
                                    <td>
                                        <div class="actions-cell">
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg></button>
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="role-name">Agency Admin</span></td>
                                    <td><span class="role-badge manager">Руководитель</span></td>
                                    <td>3</td>
                                    <td style="color:var(--text-muted);font-size:13px">Управляет CRM-базой: объекты, сделки, распределение лидов, проверка качества</td>
                                    <td>
                                        <div class="actions-cell">
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg></button>
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="role-name">Office Director</span></td>
                                    <td><span class="role-badge manager">Руководитель</span></td>
                                    <td>4</td>
                                    <td style="color:var(--text-muted);font-size:13px">Управляет офисом: агенты, отделы, планы, аналитика в рамках офиса</td>
                                    <td>
                                        <div class="actions-cell">
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg></button>
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="role-name">Office Admin</span></td>
                                    <td><span class="role-badge agent">Сотрудник</span></td>
                                    <td>8</td>
                                    <td style="color:var(--text-muted);font-size:13px">Операционная поддержка: звонки, документы, корректность CRM</td>
                                    <td>
                                        <div class="actions-cell">
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg></button>
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="role-name">Team Manager</span></td>
                                    <td><span class="role-badge agent">Сотрудник</span></td>
                                    <td>12</td>
                                    <td style="color:var(--text-muted);font-size:13px">Управляет группой агентов, контролирует планы, перераспределяет лиды</td>
                                    <td>
                                        <div class="actions-cell">
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg></button>
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="role-name">Agent</span></td>
                                    <td><span class="role-badge agent">Сотрудник</span></td>
                                    <td>72</td>
                                    <td style="color:var(--text-muted);font-size:13px">Риелтор: работа с клиентами, объектами, сделками и лидами</td>
                                    <td>
                                        <div class="actions-cell">
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg></button>
                                            <button class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ===== SECTION: PERMISSIONS ===== -->
                <div class="settings-section" id="section-permissions">
                    <div class="settings-breadcrumb">
                        <a href="#">Настройки</a> <span>›</span> <span class="current">Разрешения</span>
                    </div>
                    <div class="section-header">
                        <div>
                            <h2>Матрица разрешений</h2>
                            <p>Детальная настройка прав доступа по ролям</p>
                        </div>
                    </div>

                    <div class="content-tabs">
                        <button class="content-tab active" data-perm-tab="objects">Объекты</button>
                        <button class="content-tab" data-perm-tab="clients">Клиенты</button>
                        <button class="content-tab" data-perm-tab="deals">Сделки</button>
                        <button class="content-tab" data-perm-tab="reports">Отчёты</button>
                        <button class="content-tab" data-perm-tab="settings">Настройки</button>
                    </div>

                    <!-- TAB: Objects -->
                    <div class="card perm-tab-content active" id="perm-objects">
                        <div class="card-body" style="padding:0;overflow-x:auto;">
                            <table class="perm-matrix">
                                <thead>
                                <tr>
                                    <th style="min-width:200px">Действие</th>
                                    <th>Super Admin</th>
                                    <th>System Admin</th>
                                    <th>Agency Dir</th>
                                    <th>Agency Admin</th>
                                    <th>Office Dir</th>
                                    <th>Office Admin</th>
                                    <th>Team Mgr</th>
                                    <th>Agent</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="perm-section-divider"><td colspan="9">Базовые права</td></tr>
                                <tr>
                                    <td>Просмотр объектов</td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                </tr>
                                <tr>
                                    <td>Создание объектов</td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check"></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                </tr>
                                <tr>
                                    <td>Редактирование объектов</td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check"></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                </tr>
                                <tr>
                                    <td>Удаление объектов</td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check"></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check"></td>
                                    <td><input type="checkbox" class="perm-check"></td>
                                    <td><input type="checkbox" class="perm-check"></td>
                                </tr>
                                <tr class="perm-section-divider"><td colspan="9">Дополнительные права</td></tr>
                                <tr>
                                    <td>Экспорт данных</td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check"></td>
                                    <td><input type="checkbox" class="perm-check"></td>
                                    <td><input type="checkbox" class="perm-check"></td>
                                </tr>
                                <tr>
                                    <td>Просмотр чужих объектов</td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check"></td>
                                </tr>
                                <tr>
                                    <td>Массовое редактирование</td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check" checked></td>
                                    <td><input type="checkbox" class="perm-check"></td>
                                    <td><input type="checkbox" class="perm-check"></td>
                                    <td><input type="checkbox" class="perm-check"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB: Clients (placeholder) -->
                    <div class="card perm-tab-content" id="perm-clients">
                        <div class="card-body" style="padding:20px;text-align:center;color:var(--text-muted);">
                            Раздел в разработке
                        </div>
                    </div>

                    <!-- TAB: Deals (placeholder) -->
                    <div class="card perm-tab-content" id="perm-deals">
                        <div class="card-body" style="padding:20px;text-align:center;color:var(--text-muted);">
                            Раздел в разработке
                        </div>
                    </div>

                    <!-- TAB: Reports (placeholder) -->
                    <div class="card perm-tab-content" id="perm-reports">
                        <div class="card-body" style="padding:20px;text-align:center;color:var(--text-muted);">
                            Раздел в разработке
                        </div>
                    </div>

                    <!-- TAB: Settings (placeholder) -->
                    <div class="card perm-tab-content" id="perm-settings">
                        <div class="card-body" style="padding:20px;text-align:center;color:var(--text-muted);">
                            Раздел в разработке
                        </div>
                    </div>

                    <div class="save-bar">
                        <button class="btn btn-outline">Отменить</button>
                        <button class="btn btn-primary">Сохранить изменения</button>
                    </div>
                </div>



            </div>
        </div>
    </div>

    <!-- DRAWER: Add Role -->
    <div class="drawer-overlay" id="drawerOverlay"></div>
    <div class="drawer" id="drawerAddRole">
        <div class="drawer-header">
            <div class="drawer-header-content">
                <div class="drawer-header-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <h3>Новая роль</h3>
                    <p class="drawer-subtitle">Создайте роль и настройте уровень доступа</p>
                </div>
            </div>
            <button class="drawer-close" id="drawerClose">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="drawer-body">
            <div class="drawer-section">
                <div class="drawer-section-title">Основная информация</div>
                <div class="form-group">
                    <label class="form-label">Название роли <span class="required">*</span></label>
                    <input class="form-input" type="text" placeholder="Например: Content Manager" required>
                    <span class="form-hint">Уникальное название для идентификации роли</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Системное имя <span class="required">*</span></label>
                    <input class="form-input" type="text" placeholder="content_manager" required>
                    <span class="form-hint">Латиницей, без пробелов (используется в API)</span>
                </div>
            </div>
            <div class="drawer-divider"></div>
            <div class="drawer-section">
                <div class="drawer-section-title">Классификация</div>
                <div class="form-group">
                    <label class="form-label">Тип роли <span class="required">*</span></label>
                    <div class="drawer-type-cards">
                        <label class="drawer-type-card">
                            <input type="radio" name="roleType" value="admin">
                            <div class="drawer-type-card-inner admin">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                <span>Администратор</span>
                            </div>
                        </label>
                        <label class="drawer-type-card">
                            <input type="radio" name="roleType" value="manager">
                            <div class="drawer-type-card-inner manager">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4-4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <span>Руководитель</span>
                            </div>
                        </label>
                        <label class="drawer-type-card">
                            <input type="radio" name="roleType" value="agent">
                            <div class="drawer-type-card-inner agent">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="8.5" cy="7" r="4"/></svg>
                                <span>Сотрудник</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="drawer-divider"></div>
            <div class="drawer-section">
                <div class="drawer-section-title">Дополнительно</div>
                <div class="form-group">
                    <label class="form-label">Описание</label>
                    <textarea class="form-input" placeholder="Краткое описание обязанностей и прав роли..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Скопировать права из</label>
                    <select class="form-input form-select">
                        <option value="" selected>Не копировать</option>
                        <option value="agent">Agent</option>
                        <option value="team_manager">Team Manager</option>
                        <option value="office_admin">Office Admin</option>
                        <option value="office_director">Office Director</option>
                    </select>
                    <span class="form-hint">Новая роль унаследует все разрешения выбранной</span>
                </div>
            </div>
        </div>
        <div class="drawer-footer">
            <button class="btn btn-outline" id="drawerCancel">Отменить</button>
            <button class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Создать роль
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/settings/settings.js') }}"></script>
@endpush
