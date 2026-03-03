{{-- Drawer: Add/Edit Zone --}}
<div class="drawer-overlay" id="drawerZoneOverlay"></div>
<div class="drawer" id="drawerAddZone">
    <div class="drawer-header">
        <div class="drawer-header-content">
            <div class="drawer-header-icon" style="background: linear-gradient(135deg, #1a8a4a 0%, var(--success) 100%);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <path d="M3 9h18"/><path d="M9 3v18"/>
                </svg>
            </div>
            <div>
                <h3 id="zoneDrawerTitle">Новый микрорайон</h3>
                <p class="drawer-subtitle" id="zoneDrawerSubtitle">Добавьте микрорайон или зону внутри города</p>
            </div>
        </div>
        <button class="drawer-close" id="drawerZoneClose">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
    <form id="zoneForm" method="POST" action="{{ route('settings.zones.store') }}">
        @csrf
        <input type="hidden" name="_method" id="zoneMethod" value="POST">

        <div class="drawer-body">
            <div class="drawer-section">
                <div class="drawer-section-title">Основная информация</div>
                <div class="form-group">
                    <label class="form-label">Название <span class="required">*</span></label>
                    <input class="form-input" type="text" name="name" id="zoneName"
                           placeholder="Например: Аркадия" required>
                </div>
            </div>
            <div class="drawer-divider"></div>
            <div class="drawer-section">
                <div class="drawer-section-title">Привязка</div>
                <div class="form-group">
                    <label class="form-label">Город <span class="required">*</span></label>
                    <select name="city_id" id="zone-city-select" class="form-input" required>
                        <option value="">Выберите город...</option>
                        @if(isset($zoneCities))
                            @foreach($zoneCities as $city)
                                <option value="{{ $city->id }}" data-country-name="{{ $city->state->country->name ?? '' }}">{{ $city->name }} ({{ $city->state->name ?? '' }})</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Регион</label>
                    <select name="district_id" id="zone-district-select" class="form-input">
                        <option value="">Без региона</option>
                    </select>
                    <span class="form-hint">Необязательно — микрорайон может не принадлежать региону</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Страна <span class="required">*</span></label>
                    <input type="text" class="form-input" id="zone-country-display" readonly placeholder="Определится автоматически" style="background:#f9fafb;color:var(--text-muted);">
                </div>
            </div>
        </div>

        <div class="drawer-footer">
            <button type="button" class="btn btn-outline" id="drawerZoneCancel">Отмена</button>
            <button type="submit" class="btn btn-primary" id="zoneSubmitBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Создать
            </button>
        </div>
    </form>
</div>
