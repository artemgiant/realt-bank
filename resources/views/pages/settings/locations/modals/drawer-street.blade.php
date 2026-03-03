{{-- Drawer: Add/Edit Street --}}
<div class="drawer-overlay" id="drawerStreetOverlay"></div>
<div class="drawer" id="drawerAddStreet">
    <div class="drawer-header">
        <div class="drawer-header-content">
            <div class="drawer-header-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18"/><path d="M6 6l12 12"/>
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                </svg>
            </div>
            <div>
                <h3 id="streetDrawerTitle">Новая улица</h3>
                <p class="drawer-subtitle" id="streetDrawerSubtitle">Добавьте улицу в справочник</p>
            </div>
        </div>
        <button class="drawer-close" id="drawerStreetClose">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
    <form id="streetForm" method="POST" action="{{ route('settings.streets.store') }}">
        @csrf
        <input type="hidden" name="_method" id="streetMethod" value="POST">

        <div class="drawer-body">
            <div class="drawer-section">
                <div class="drawer-section-title">Основная информация</div>
                <div class="form-group">
                    <label class="form-label">Название улицы <span class="required">*</span></label>
                    <input class="form-input" type="text" name="name" id="streetName"
                           placeholder="Например: ул. Дерибасовская" required>
                </div>
            </div>
            <div class="drawer-divider"></div>
            <div class="drawer-section">
                <div class="drawer-section-title">Привязка</div>
                <div class="form-group">
                    <label class="form-label">Город <span class="required">*</span></label>
                    <select name="city_id" id="street-city-select" class="form-input" required>
                        <option value="">Выберите город...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Микрорайон</label>
                    <select name="zone_id" id="street-zone-select" class="form-input">
                        <option value="">Без микрорайона</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Регион</label>
                    <select name="district_id" id="street-district-select" class="form-input">
                        <option value="">Без региона</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Область</label>
                    <select id="street-state-filter" class="form-input">
                        <option value="">Все области...</option>
                        @if(isset($statesList))
                            @foreach($statesList as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <span class="form-hint">Фильтр для выбора города</span>
                </div>
            </div>
        </div>

        <div class="drawer-footer">
            <button type="button" class="btn btn-outline" id="drawerStreetCancel">Отмена</button>
            <button type="submit" class="btn btn-primary" id="streetSubmitBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Создать
            </button>
        </div>
    </form>
</div>
