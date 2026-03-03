{{-- Drawer: Add/Edit City --}}
<div class="drawer-overlay" id="drawerCityOverlay"></div>
<div class="drawer" id="drawerAddCity">
    <div class="drawer-header">
        <div class="drawer-header-content">
            <div class="drawer-header-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/>
                </svg>
            </div>
            <div>
                <h3 id="cityDrawerTitle">Новый город</h3>
                <p class="drawer-subtitle" id="cityDrawerSubtitle">Добавьте город или населённый пункт</p>
            </div>
        </div>
        <button class="drawer-close" id="drawerCityClose">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
    <form id="cityForm" method="POST" action="{{ route('settings.cities.store') }}">
        @csrf
        <input type="hidden" name="_method" id="cityMethod" value="POST">

        <div class="drawer-body">
            <div class="drawer-section">
                <div class="drawer-section-title">Основная информация</div>
                <div class="form-group">
                    <label class="form-label">Название <span class="required">*</span></label>
                    <input class="form-input" type="text" name="name" id="cityName"
                           placeholder="Например: Одесса" required>
                </div>
            </div>
            <div class="drawer-divider"></div>
            <div class="drawer-section">
                <div class="drawer-section-title">Привязка</div>
                <div class="form-group">
                    <label class="form-label">Регион <span class="required">*</span></label>
                    <select name="state_id" id="city-state-select" class="form-input" required>
                        <option value="">Выберите регион...</option>
                        @if(isset($statesList))
                            @foreach($statesList as $state)
                                <option value="{{ $state->id }}" data-country-name="{{ $state->country->name ?? '' }}">{{ $state->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Страна <span class="required">*</span></label>
                    <input class="form-input" type="text" id="city-country-display" readonly placeholder="Выберите регион...">
                </div>
            </div>
        </div>

        <div class="drawer-footer">
            <button type="button" class="btn btn-outline" id="drawerCityCancel">Отмена</button>
            <button type="submit" class="btn btn-primary" id="citySubmitBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Создать
            </button>
        </div>
    </form>
</div>
