{{-- Drawer: Add/Edit User --}}
<div class="drawer-overlay" id="drawerUserOverlay"></div>
<div class="drawer" id="drawerAddUser">
    <div class="drawer-header">
        <div class="drawer-header-content">
            <div class="drawer-header-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <h3>Новый пользователь</h3>
                <p class="drawer-subtitle">Создайте учётную запись и назначьте роль</p>
            </div>
        </div>
        <button class="drawer-close" id="drawerUserClose">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
    <form id="userForm" method="POST" action="{{ route('settings.users.store') }}">
        @csrf
        <input type="hidden" name="_method" id="userMethod" value="POST">

        <div class="drawer-body">
            {{-- Основная информация --}}
            <div class="drawer-section">
                <div class="drawer-section-title">Основная информация</div>
                <div class="form-group">
                    <label class="form-label">Имя пользователя <span class="required">*</span></label>
                    <input class="form-input" type="text" name="name" id="userName"
                           placeholder="Например: Иван Петров" required>
                    <span class="form-hint">Отображаемое имя в системе</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Email <span class="required">*</span></label>
                    <input class="form-input" type="email" name="email" id="userEmail"
                           placeholder="ivan.petrov@company.com" required>
                    <span class="form-hint">Используется для входа в систему</span>
                </div>
                <div class="form-group" id="passwordGroup">
                    <label class="form-label">Пароль <span class="required">*</span></label>
                    <input class="form-input" type="password" name="password" id="userPassword"
                           placeholder="Минимум 8 символов" required>
                </div>
            </div>

            <div class="drawer-divider"></div>

            {{-- Роль и доступ --}}
            <div class="drawer-section">
                <div class="drawer-section-title">Роль и доступ</div>
                <div class="form-group">
                    <label class="form-label">Роль <span class="required">*</span></label>
                    <select id="user-role-select" name="role_id" class="drawer-select2" required>
                        <option value="">Выберите роль...</option>
                        @isset($roles)
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->display_name ?? $role->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
            </div>

            <div class="drawer-divider"></div>

            {{-- Привязка к сотруднику --}}
            <div class="drawer-section">
                <div class="drawer-section-title">Привязка к сотруднику</div>
                <div class="form-group">
                    <label class="form-label">Сотрудник</label>
                    <select id="user-employee-select" name="employee_id" class="drawer-select2">
                        <option value="">Выберите сотрудника...</option>
                        @isset($employees)
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                            @endforeach
                        @endisset
                    </select>
                    <span class="form-hint">Связать учётную запись с карточкой сотрудника (опционально)</span>
                </div>
            </div>

            <div class="drawer-divider"></div>

            {{-- Статус --}}
            <div class="drawer-section">
                <div class="drawer-section-title">Статус</div>
                <div class="toggle-row" style="border-bottom:none;padding:0;">
                    <div class="toggle-info">
                        <h4>Активный пользователь</h4>
                        <p>Пользователь может входить в систему</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" id="userActive" value="1" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="drawer-footer">
            <button type="button" class="btn btn-outline" id="drawerUserCancel">Отменить</button>
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Создать пользователя
            </button>
        </div>
    </form>
</div>
