{{-- Section: Permissions --}}
<div class="settings-section {{ ($activeSection ?? '') === 'permissions' ? 'active' : '' }}" id="section-permissions">
    <div class="settings-breadcrumb">
        <a href="#">Настройки</a>
        <span>›</span>
        <span class="current">Разрешения</span>
    </div>
    <div class="section-header">
        <div>
            <h2>Матрица разрешений</h2>
            <p>Детальная настройка прав доступа по ролям</p>
        </div>
    </div>

    <div class="content-tabs">
        <button class="content-tab active" data-perm-tab="objects">Объекты</button>
        <button class="content-tab" data-perm-tab="clients">Клиенты</button>
        <button class="content-tab" data-perm-tab="deals">Сделки</button>
        <button class="content-tab" data-perm-tab="reports">Отчёты</button>
        <button class="content-tab" data-perm-tab="settings">Настройки</button>
    </div>

    {{-- TAB: Objects --}}
    <div class="card perm-tab-content active" id="perm-objects">
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table class="perm-matrix">
                <thead>
                <tr>
                    <th style="min-width:200px">Действие</th>
                    <th>Super Admin</th>
                    <th>System Admin</th>
                    <th>Agency Dir</th>
                    <th>Agency Admin</th>
                    <th>Office Dir</th>
                    <th>Office Admin</th>
                    <th>Team Mgr</th>
                    <th>Agent</th>
                </tr>
                </thead>
                <tbody>
                <tr class="perm-section-divider">
                    <td colspan="9">Базовые права</td>
                </tr>
                <tr>
                    <td>Просмотр объектов</td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                </tr>
                <tr>
                    <td>Создание объектов</td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check"></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                </tr>
                <tr>
                    <td>Редактирование объектов</td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check"></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                </tr>
                <tr>
                    <td>Удаление объектов</td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check"></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check"></td>
                    <td><input type="checkbox" class="perm-check"></td>
                    <td><input type="checkbox" class="perm-check"></td>
                </tr>
                <tr class="perm-section-divider">
                    <td colspan="9">Дополнительные права</td>
                </tr>
                <tr>
                    <td>Экспорт данных</td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check"></td>
                    <td><input type="checkbox" class="perm-check"></td>
                    <td><input type="checkbox" class="perm-check"></td>
                </tr>
                <tr>
                    <td>Просмотр чужих объектов</td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check"></td>
                </tr>
                <tr>
                    <td>Массовое редактирование</td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check" checked></td>
                    <td><input type="checkbox" class="perm-check"></td>
                    <td><input type="checkbox" class="perm-check"></td>
                    <td><input type="checkbox" class="perm-check"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- TAB: Clients --}}
    <div class="card perm-tab-content" id="perm-clients">
        <div class="card-body" style="padding:20px;text-align:center;color:var(--text-muted);">
            Раздел в разработке
        </div>
    </div>

    {{-- TAB: Deals --}}
    <div class="card perm-tab-content" id="perm-deals">
        <div class="card-body" style="padding:20px;text-align:center;color:var(--text-muted);">
            Раздел в разработке
        </div>
    </div>

    {{-- TAB: Reports --}}
    <div class="card perm-tab-content" id="perm-reports">
        <div class="card-body" style="padding:20px;text-align:center;color:var(--text-muted);">
            Раздел в разработке
        </div>
    </div>

    {{-- TAB: Settings --}}
    <div class="card perm-tab-content" id="perm-settings">
        <div class="card-body" style="padding:20px;text-align:center;color:var(--text-muted);">
            Раздел в разработке
        </div>
    </div>

    <div class="save-bar">
        <button class="btn btn-outline">Отменить</button>
        <button class="btn btn-primary">Сохранить изменения</button>
    </div>
</div>
