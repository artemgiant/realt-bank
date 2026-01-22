
<div class="lf-root" id="lfRoot">
    <div class="lf-container" id="lfContainer">
        <div class="lf-input" id="lfInput">
            <div class="lf-tags" id="lfTags">
                <div id="lfLocationTag"></div>
                <div class="lf-detail-wrap" id="lfDetailWrap">
                    <div class="lf-tooltip" id="lfTooltip"></div>
                </div>
                <input type="text" id="lfSearch" placeholder="Страна, область, город...">
            </div>
            <div class="lf-buttons">
                <button type="button" class="lf-btn lf-btn-clear" id="lfClear">×</button>
                <button type="button" class="lf-btn" id="lfToggle">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="lf-dropdown" id="lfDropdown">
            <div class="lf-modes" id="lfModes">
                <button class="lf-active" data-mode="location">Страна, регион, город</button>
                <button data-mode="detail">Локация</button>
            </div>

            <div class="lf-categories" id="lfCategories">
                <button class="lf-active" data-cat="all">Все</button>
                <button data-cat="districts">Районы</button>
                <button data-cat="streets">Улицы</button>
                <button data-cat="landmarks">Микрорайоны</button>
                <button data-cat="developers">Девелоперы</button>
            </div>

            <div class="lf-breadcrumbs" id="lfBreadcrumbs"></div>

            <div class="lf-content" id="lfContent">
                <div class="lf-section lf-hidden" id="lf-countries"><div class="lf-section-title">Страны</div><ul></ul></div>
                <div class="lf-section lf-hidden" id="lf-regions"><div class="lf-section-title">Регионы</div><ul></ul></div>
                <div class="lf-section lf-hidden" id="lf-cities"><div class="lf-section-title">Города</div><ul></ul></div>
                <div class="lf-section lf-hidden" id="lf-districts"><div class="lf-section-title">Районы</div><ul></ul></div>
                <div class="lf-section lf-hidden" id="lf-streets"><div class="lf-section-title">Улицы</div><ul></ul></div>
                <div class="lf-section lf-hidden" id="lf-landmarks"><div class="lf-section-title">Зоны/Микрорайоны</div><ul></ul></div>
                <div class="lf-section lf-hidden" id="lf-developers"><div class="lf-section-title">Девелоперы</div><ul></ul></div>
                <div class="lf-empty" id="lfEmpty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                    </svg>
                    <div>Ничего не найдено</div>
                </div>
            </div>
        </div>

        <input type="hidden" id="lfType" name="location_type">
        <input type="hidden" id="lfId" name="location_id">
        <input type="hidden" id="lfDetails" name="detail_ids">
    </div>
</div>
