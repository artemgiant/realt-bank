{{-- Drawer: Add/Edit Country --}}
<div class="drawer-overlay" id="drawerCountryOverlay"></div>
<div class="drawer" id="drawerAddCountry">
    <div class="drawer-header">
        <div class="drawer-header-content">
            <div class="drawer-header-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>
                </svg>
            </div>
            <div>
                <h3 id="countryDrawerTitle">Новая страна</h3>
                <p class="drawer-subtitle" id="countryDrawerSubtitle">Добавьте страну для географической структуры</p>
            </div>
        </div>
        <button class="drawer-close" id="drawerCountryClose">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
    <form id="countryForm" method="POST" action="{{ route('settings.countries.store') }}">
        @csrf
        <input type="hidden" name="_method" id="countryMethod" value="POST">

        <div class="drawer-body">
            <div class="drawer-section">
                <div class="drawer-section-title">Основная информация</div>
                <div class="form-group">
                    <label class="form-label">Название страны <span class="required">*</span></label>
                    <input class="form-input" type="text" name="name" id="countryName"
                           placeholder="Например: Украина" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Код страны <span class="required">*</span></label>
                    <input class="form-input" type="text" name="code" id="countryCode"
                           placeholder="Например: UA" maxlength="10" required>
                </div>
            </div>
        </div>

        <div class="drawer-footer">
            <button type="button" class="btn btn-outline" id="drawerCountryCancel">Отмена</button>
            <button type="submit" class="btn btn-primary" id="countrySubmitBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Создать
            </button>
        </div>
    </form>
</div>
