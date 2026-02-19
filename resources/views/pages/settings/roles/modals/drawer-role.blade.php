{{-- Drawer: Add/Edit Role --}}
<div class="drawer-overlay" id="drawerOverlay"></div>
<div class="drawer" id="drawerAddRole">
    <div class="drawer-header">
        <div class="drawer-header-content">
            <div class="drawer-header-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <div>
                <h3>Новая роль</h3>
                <p class="drawer-subtitle">Создайте роль и настройте уровень доступа</p>
            </div>
        </div>
        <button class="drawer-close" id="drawerClose">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
    <form id="roleForm" method="POST" action="{{ route('settings.roles.store') }}">
        @csrf
        <input type="hidden" name="_method" id="roleMethod" value="POST">

        <div class="drawer-body">
            <div class="drawer-section">
                <div class="drawer-section-title">Основная информация</div>
                <div class="form-group">
                    <label class="form-label">Название роли <span class="required">*</span></label>
                    <input class="form-input" type="text" name="display_name" id="roleDisplayName"
                           placeholder="Например: Content Manager" required>
                    <span class="form-hint">Отображаемое название роли</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Системное имя <span class="required">*</span></label>
                    <input class="form-input" type="text" name="name" id="roleName"
                           placeholder="content_manager" required>
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
                            <input type="radio" name="type" value="admin">
                            <div class="drawer-type-card-inner admin">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                                <span>Администратор</span>
                            </div>
                        </label>
                        <label class="drawer-type-card">
                            <input type="radio" name="type" value="manager">
                            <div class="drawer-type-card-inner manager">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <span>Руководитель</span>
                            </div>
                        </label>
                        <label class="drawer-type-card">
                            <input type="radio" name="type" value="agent" checked>
                            <div class="drawer-type-card-inner agent">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                    <circle cx="8.5" cy="7" r="4"/>
                                </svg>
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
                    <textarea class="form-input" name="description" id="roleDescription"
                              placeholder="Краткое описание обязанностей и прав роли..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Скопировать права из</label>
                    <select id="role-copy-from-select" name="copy_from" class="drawer-select2">
                        <option value="">Не копировать</option>
                        @isset($roles)
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->display_name ?? $r->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                    <span class="form-hint">Новая роль унаследует все разрешения выбранной</span>
                </div>
            </div>
        </div>
        <div class="drawer-footer">
            <button type="button" class="btn btn-outline" id="drawerCancel">Отменить</button>
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Создать роль
            </button>
        </div>
    </form>
</div>
