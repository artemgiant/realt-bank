{{-- Drawer: Add User --}}
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
    <div class="drawer-body">
        {{-- Основная информация --}}
        <div class="drawer-section">
            <div class="drawer-section-title">Основная информация</div>
            <div class="form-group">
                <label class="form-label">Имя пользователя <span class="required">*</span></label>
                <input class="form-input" type="text" placeholder="Например: Иван Петров" required>
                <span class="form-hint">Отображаемое имя в системе</span>
            </div>
            <div class="form-group">
                <label class="form-label">Email <span class="required">*</span></label>
                <input class="form-input" type="email" placeholder="ivan.petrov@company.com" required>
                <span class="form-hint">Используется для входа в систему</span>
            </div>
            <div class="form-group">
                <label class="form-label">Пароль <span class="required">*</span></label>
                <input class="form-input" type="password" placeholder="Минимум 8 символов" required>
            </div>
        </div>

        <div class="drawer-divider"></div>

        {{-- Роль и доступ --}}
        <div class="drawer-section">
            <div class="drawer-section-title">Роль и доступ</div>
            <div class="form-group">
                <label class="form-label">Роль <span class="required">*</span></label>
                <select id="user-role-select" class="drawer-select2" required>
                    <option value="" selected>Выберите роль...</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="system_admin">System Admin</option>
                    <option value="agency_director">Agency Director</option>
                    <option value="agency_admin">Agency Admin</option>
                    <option value="office_director">Office Director</option>
                    <option value="office_admin">Office Admin</option>
                    <option value="team_manager">Team Manager</option>
                    <option value="agent">Agent</option>
                </select>
            </div>
        </div>

        <div class="drawer-divider"></div>

        {{-- Привязка к сотруднику --}}
        <div class="drawer-section">
            <div class="drawer-section-title">Привязка к сотруднику</div>
            <div class="form-group">
                <label class="form-label">Сотрудник</label>
                <select id="user-employee-select" class="drawer-select2">
                    <option value="" selected>Выберите сотрудника...</option>
                    <option value="1">Волков Владимир Александрович</option>
                    <option value="2">Коваленко Анна Игоревна</option>
                    <option value="3">Петренко Дмитрий Олегович</option>
                    <option value="4">Шевченко Елена Викторовна</option>
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
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>
    <div class="drawer-footer">
        <button class="btn btn-outline" id="drawerUserCancel">Отменить</button>
        <button class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Создать пользователя
        </button>
    </div>
</div>
