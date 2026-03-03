{{-- Drawer: Add/Edit District --}}
<div class="drawer-overlay" id="drawerDistrictOverlay"></div>
<div class="drawer" id="drawerAddDistrict">
    <div class="drawer-header">
        <div class="drawer-header-content">
            <div class="drawer-header-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="10" r="3"/>
                    <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 10-16 0c0 3 2.7 7 8 11.7z"/>
                </svg>
            </div>
            <div>
                <h3 id="districtDrawerTitle">Новый район</h3>
                <p class="drawer-subtitle" id="districtDrawerSubtitle">Добавьте район внутри города</p>
            </div>
        </div>
        <button class="drawer-close" id="drawerDistrictClose">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
    <form id="districtForm" method="POST" action="{{ route('settings.districts.store') }}">
        @csrf
        <input type="hidden" name="_method" id="districtMethod" value="POST">

        <div class="drawer-body">
            <div class="drawer-section">
                <div class="drawer-section-title">Основная информация</div>
                <div class="form-group">
                    <label class="form-label">Название района <span class="required">*</span></label>
                    <input class="form-input" type="text" name="name" id="districtName"
                           placeholder="Например: Одесский район" required>
                </div>
            </div>
            <div class="drawer-divider"></div>
            <div class="drawer-section">
                <div class="drawer-section-title">Привязка</div>
                <div class="form-group">
                    <label class="form-label">Город <span class="required">*</span></label>
                    <select name="city_id" id="district-city-select" class="form-input" required>
                        <option value="">Выберите город...</option>
                        @if(isset($allCities))
                            @foreach($allCities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }} ({{ $city->state->name ?? '' }})</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="drawer-footer">
            <button type="button" class="btn btn-outline" id="drawerDistrictCancel">Отмена</button>
            <button type="submit" class="btn btn-primary" id="districtSubmitBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Создать
            </button>
        </div>
    </form>
</div>
