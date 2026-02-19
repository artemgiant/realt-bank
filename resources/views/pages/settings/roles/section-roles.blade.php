{{-- Section: Roles --}}
<div class="settings-section {{ $activeSection === 'roles' ? 'active' : '' }}" id="section-roles">
    <div class="settings-breadcrumb">
        <a href="#">Настройки</a>
        <span>›</span>
        <span class="current">Роли</span>
    </div>
    <div class="section-header">
        <div>
            <h2>Роли пользователей</h2>
            <p>Управляйте ролями и назначайте уровни доступа</p>
        </div>
        <button class="btn btn-primary" onclick="openRoleDrawer()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
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
                @forelse($roles as $role)
                    <tr>
                        <td><span class="role-name">{{ $role->display_name ?? $role->name }}</span></td>
                        <td>
                            @php
                                $badgeClass = match($role->type ?? 'agent') {
                                    'admin' => 'admin',
                                    'manager' => 'manager',
                                    default => 'agent'
                                };
                                $badgeText = match($role->type ?? 'agent') {
                                    'admin' => 'Администратор',
                                    'manager' => 'Руководитель',
                                    default => 'Сотрудник'
                                };
                            @endphp
                            <span class="role-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                        </td>
                        <td>{{ $role->users_count }}</td>
                        <td style="color:var(--text-muted);font-size:13px">{{ $role->description ?? '—' }}</td>
                        <td>
                            <div class="actions-cell">
                                <button class="btn-icon" title="Редактировать" onclick="openRoleDrawer({{ $role->id }})">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                        <path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/>
                                    </svg>
                                </button>
                                <button class="btn-icon btn-delete" title="Удалить"
                                        data-id="{{ $role->id }}"
                                        data-type="role"
                                        data-name="{{ $role->display_name ?? $role->name }}"
                                        data-users="{{ $role->users_count }}"
                                        onclick="openDeleteModal(this)">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 40px; color: var(--text-muted);">
                            Нет созданных ролей
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
