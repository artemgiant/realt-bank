{{-- Section: Roles --}}
<div class="settings-section" id="section-roles">
    <div class="settings-breadcrumb">
        <a href="#">Настройки</a>
        <span>›</span>
        <span class="current">Роли</span>
    </div>
    <div class="section-header">
        <div>
            <h2>Роли пользователей</h2>
            <p>Управляйте ролями и назначайте уровни доступа</p>
        </div>
        <button class="btn btn-primary" onclick="openDrawer()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Добавить роль
        </button>
    </div>

    <div class="card">
        <div class="card-body" style="padding:0;">
            <table class="roles-table">
                <thead>
                <tr>
                    <th>Роль</th>
                    <th>Тип</th>
                    <th>Пользователей</th>
                    <th>Описание</th>
                    <th style="width:100px">Действия</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><span class="role-name">Super Admin</span></td>
                    <td><span class="role-badge admin">Администратор</span></td>
                    <td>1</td>
                    <td style="color:var(--text-muted);font-size:13px">Абсолютный контроль над всеми разделами, правами, офисами, статистикой</td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon" title="Редактировать">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/>
                                </svg>
                            </button>
                            <button class="btn-icon btn-delete" title="Удалить" data-type="role" data-name="Super Admin" data-users="1">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="role-name">System Admin</span></td>
                    <td><span class="role-badge admin">Администратор</span></td>
                    <td>2</td>
                    <td style="color:var(--text-muted);font-size:13px">Техническая часть CRM: роли, настройка полей, API, интеграции</td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon" title="Редактировать">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/>
                                </svg>
                            </button>
                            <button class="btn-icon btn-delete" title="Удалить" data-type="role" data-name="System Admin" data-users="2">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="role-name">Agency Director</span></td>
                    <td><span class="role-badge manager">Руководитель</span></td>
                    <td>1</td>
                    <td style="color:var(--text-muted);font-size:13px">Стратегия, финансы, структура офисов. Полный обзор всех офисов и отчётов</td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon" title="Редактировать">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/>
                                </svg>
                            </button>
                            <button class="btn-icon btn-delete" title="Удалить" data-type="role" data-name="Agency Director" data-users="1">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="role-name">Agency Admin</span></td>
                    <td><span class="role-badge manager">Руководитель</span></td>
                    <td>3</td>
                    <td style="color:var(--text-muted);font-size:13px">Управляет CRM-базой: объекты, сделки, распределение лидов, проверка качества</td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon" title="Редактировать">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/>
                                </svg>
                            </button>
                            <button class="btn-icon btn-delete" title="Удалить" data-type="role" data-name="Agency Admin" data-users="3">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="role-name">Office Director</span></td>
                    <td><span class="role-badge manager">Руководитель</span></td>
                    <td>4</td>
                    <td style="color:var(--text-muted);font-size:13px">Управляет офисом: агенты, отделы, планы, аналитика в рамках офиса</td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon" title="Редактировать">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/>
                                </svg>
                            </button>
                            <button class="btn-icon btn-delete" title="Удалить" data-type="role" data-name="Office Director" data-users="4">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="role-name">Office Admin</span></td>
                    <td><span class="role-badge agent">Сотрудник</span></td>
                    <td>8</td>
                    <td style="color:var(--text-muted);font-size:13px">Операционная поддержка: звонки, документы, корректность CRM</td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon" title="Редактировать">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/>
                                </svg>
                            </button>
                            <button class="btn-icon btn-delete" title="Удалить" data-type="role" data-name="Office Admin" data-users="8">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="role-name">Team Manager</span></td>
                    <td><span class="role-badge agent">Сотрудник</span></td>
                    <td>12</td>
                    <td style="color:var(--text-muted);font-size:13px">Управляет группой агентов, контролирует планы, перераспределяет лиды</td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon" title="Редактировать">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/>
                                </svg>
                            </button>
                            <button class="btn-icon btn-delete" title="Удалить" data-type="role" data-name="Team Manager" data-users="12">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="role-name">Agent</span></td>
                    <td><span class="role-badge agent">Сотрудник</span></td>
                    <td>72</td>
                    <td style="color:var(--text-muted);font-size:13px">Риелтор: работа с клиентами, объектами, сделками и лидами</td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon" title="Редактировать">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/>
                                </svg>
                            </button>
                            <button class="btn-icon btn-delete" title="Удалить" data-type="role" data-name="Agent" data-users="72">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
