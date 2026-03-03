{{-- Drawer: Add/Edit State --}}
<div class="drawer-overlay" id="drawerStateOverlay"></div>
<div class="drawer" id="drawerAddState">
    <div class="drawer-header">
        <div class="drawer-header-content">
            <div class="drawer-header-icon icon-orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/>
                </svg>
            </div>
            <div>
                <h3 id="stateDrawerTitle">Новый регион</h3>
                <p class="drawer-subtitle" id="stateDrawerSubtitle">Добавьте регион для географической структуры</p>
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
                    <label class="form-label">Название региона <span class="required">*</span></label>
                    <input class="form-input" type="text" name="name" id="stateName"
                           placeholder="Например: Одесская область" required>
                </div>
            </div>
            <div class="drawer-divider"></div>
            <div class="drawer-section">
                <div class="drawer-section-title">Привязка</div>
                <div class="form-group">
                    <label class="form-label">Страна <span class="required">*</span></label>
                    <select name="country_id" id="state-country-select" class="form-input" required>
                        <option value="">Выберите страну...</option>
                        @if(isset($countriesForState))
                            @foreach($countriesForState as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        @endif
                    </select>
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
