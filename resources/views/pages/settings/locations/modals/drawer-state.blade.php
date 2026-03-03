{{-- Drawer: Add/Edit State --}}
<div class="drawer-overlay" id="drawerStateOverlay"></div>
<div class="drawer" id="drawerAddState">
    <div class="drawer-header">
        <div class="drawer-header-content">
            <div class="drawer-header-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="10" r="3"/>
                    <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 10-16 0c0 3 2.7 7 8 11.7z"/>
                </svg>
            </div>
            <div>
                <h3 id="stateDrawerTitle">Новая область</h3>
                <p class="drawer-subtitle" id="stateDrawerSubtitle">Добавьте область для географической структуры</p>
            </div>
        </div>
        <button class="drawer-close" id="drawerStateClose">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
    <form id="stateForm" method="POST" action="{{ route('settings.states.store') }}">
        @csrf
        <input type="hidden" name="_method" id="stateMethod" value="POST">

        <div class="drawer-body">
            <div class="drawer-section">
                <div class="drawer-section-title">Основная информация</div>
                <div class="form-group">
                    <label class="form-label">Название области <span class="required">*</span></label>
                    <input class="form-input" type="text" name="name" id="stateName"
                           placeholder="Например: Одесская область" required>
                </div>
            </div>
        </div>

        <div class="drawer-footer">
            <button type="button" class="btn btn-outline" id="drawerStateCancel">Отмена</button>
            <button type="submit" class="btn btn-primary" id="stateSubmitBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Создать
            </button>
        </div>
    </form>
</div>
