{{-- Drawer: Add/Edit Region (Район региона) --}}
<div class="drawer-overlay" id="drawerRegionOverlay"></div>
<div class="drawer" id="drawerAddRegion">
    <div class="drawer-header">
        <div class="drawer-header-content">
            <div class="drawer-header-icon icon-orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
            </div>
            <div>
                <h3 id="regionDrawerTitle">Новый район региона</h3>
                <p class="drawer-subtitle" id="regionDrawerSubtitle">Добавьте район внутри региона</p>
            </div>
        </div>
        <button class="drawer-close" id="drawerRegionClose">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
    <form id="regionForm" method="POST" action="{{ route('settings.regions.store') }}">
        @csrf
        <input type="hidden" name="_method" id="regionMethod" value="POST">

        <div class="drawer-body">
            <div class="drawer-section">
                <div class="drawer-section-title">Основная информация</div>
                <div class="form-group">
                    <label class="form-label">Название района региона <span class="required">*</span></label>
                    <input class="form-input" type="text" name="name" id="regionName"
                           placeholder="Например: Беляевский район" required>
                </div>
            </div>
            <div class="drawer-divider"></div>
            <div class="drawer-section">
                <div class="drawer-section-title">Привязка</div>
                <div class="form-group">
                    <label class="form-label">Регион <span class="required">*</span></label>
                    <select name="state_id" id="region-state-select" class="form-input" required>
                        <option value="">Выберите регион...</option>
                        @if(isset($statesForRegion))
                            @foreach($statesForRegion as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="drawer-footer">
            <button type="button" class="btn btn-outline" id="drawerRegionCancel">Отмена</button>
            <button type="submit" class="btn btn-primary" id="regionSubmitBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Создать
            </button>
        </div>
    </form>
</div>
