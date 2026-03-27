{{-- Section: Permissions --}}
<div class="settings-section {{ $activeSection === 'permissions' ? 'active' : '' }}" id="section-permissions">
    <div class="settings-breadcrumb">
        <a href="#">Настройки</a>
        <span>›</span>
        <span class="current">Разрешения</span>
    </div>
    <div class="section-header">
        <div>
            <h2>Матрица разрешений</h2>
            <p>Детальная настройка прав доступа по ролям</p>
        </div>
        <div class="section-header-actions">
            <button class="btn btn-outline" id="permResetBtn" style="display:none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                    <path d="M3 3v5h5"/>
                </svg>
                Сбросить
            </button>
            <button class="btn btn-primary" id="permSaveBtn" style="display:none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Сохранить изменения
            </button>
        </div>
    </div>

    <div class="content-tabs">
        @php
            $groupLabels = [
                'properties' => 'Объекты',
                'companies' => 'Компании',
                'employees' => 'Сотрудники',
                'complexes' => 'Комплексы',
                'developers' => 'Девелоперы',
                'settings' => 'Настройки',
            ];

            // Scoped permissions: base action → [office_perm, company_perm]
            // Dropdown вместо checkbox. Sub-permissions скрыты из списка.
            $scopedPermissions = [
                'properties.view' => [
                    'label' => 'Просмотр объектов',
                    'office' => 'properties.view_office',
                    'company' => 'properties.view_company',
                ],
                'properties.edit' => [
                    'label' => 'Редактирование объектов',
                    'office' => 'properties.edit_office',
                    'company' => 'properties.edit_company',
                ],
                'properties.delete' => [
                    'label' => 'Удаление объектов',
                    'office' => 'properties.delete_office',
                    'company' => 'properties.delete_company',
                ],
                'properties.reassign' => [
                    'label' => 'Смена агента у объекта',
                    'office' => 'properties.reassign_office',
                    'company' => 'properties.reassign_company',
                ],
            ];

            // Все sub-permission имена для скрытия из общего списка
            $hiddenPermissions = collect($scopedPermissions)->flatMap(fn($s) => [$s['office'], $s['company']])->toArray();
        @endphp
        @foreach($permissionGroups as $group => $perms)
            <button class="content-tab {{ $loop->first ? 'active' : '' }}" data-perm-tab="{{ $group }}">
                {{ $groupLabels[$group] ?? ucfirst($group) }}
            </button>
        @endforeach
    </div>

    @php
        $crudSuffixes = ['.view', '.create', '.edit', '.delete'];
    @endphp

    @foreach($permissionGroups as $group => $perms)
        @php
            $visiblePerms = $perms->filter(fn($p) => !in_array($p->name, $hiddenPermissions));
            $crudPerms = $visiblePerms->filter(fn($p) => collect($crudSuffixes)->contains(fn($s) => str_ends_with($p->name, $s)));
            $extraPerms = $visiblePerms->filter(fn($p) => !collect($crudSuffixes)->contains(fn($s) => str_ends_with($p->name, $s)));
        @endphp
        <div class="card perm-tab-content {{ $loop->first ? 'active' : '' }}" id="perm-{{ $group }}">
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table class="perm-matrix">
                    <thead>
                    <tr>
                        <th style="min-width:200px">Действие</th>
                        @foreach($roles as $role)
                            <th title="{{ $role->description }}">{{ $role->display_name ?? $role->name }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    {{-- Базовые CRUD права --}}
                    @foreach($crudPerms as $permission)
                        @include('pages.settings.permissions._permission-row', ['permission' => $permission, 'roles' => $roles])
                    @endforeach

                    {{-- Разделитель + дополнительные права --}}
                    @if($extraPerms->isNotEmpty())
                        <tr class="perm-section-divider">
                            <td colspan="{{ $roles->count() + 1 }}">Дополнительные права</td>
                        </tr>
                        @foreach($extraPerms as $permission)
                            @include('pages.settings.permissions._permission-row', ['permission' => $permission, 'roles' => $roles])
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <div class="perm-save-indicator" id="permSaveIndicator" style="display:none;">
        <svg class="spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
            <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
            <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/>
        </svg>
        <span>Сохранение...</span>
    </div>
</div>
