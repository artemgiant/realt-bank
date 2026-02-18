{{-- Section: Users --}}
<div class="settings-section active" id="section-users">
    <div class="settings-breadcrumb">
        <a href="#">Настройки</a>
        <span>›</span>
        <span class="current">Пользователи</span>
    </div>

    <div class="section-header">
        <div>
            <h2>Пользователи</h2>
            <p>Управление пользователями системы</p>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <div class="section-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input placeholder="Поиск пользователя...">
            </div>
            <button class="btn btn-primary" onclick="openUserDrawer()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Добавить
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding:0;">
            <table class="roles-table">
                <thead>
                <tr>
                    <th>Пользователь</th>
                    <th>Сотрудник</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Офис</th>
                    <th>Статус</th>
                    <th style="width:100px">Действия</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><span class="role-name">Владимир Волков</span></td>
                    <td style="font-size:13px">Волков Владимир Александрович</td>
                    <td style="color:var(--text-muted);font-size:13px">v.volkov@realtbank.com.ua</td>
                    <td><span class="role-badge admin">Super Admin</span></td>
                    <td style="font-size:13px">Главный офис</td>
                    <td><span class="tag tag-primary">Активен</span></td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/>
                                </svg>
                            </button>
                            <button class="btn-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="role-name">Анна Коваленко</span></td>
                    <td style="font-size:13px">Коваленко Анна Игоревна</td>
                    <td style="color:var(--text-muted);font-size:13px">a.kovalenko@realtbank.com.ua</td>
                    <td><span class="role-badge manager">Директор</span></td>
                    <td style="font-size:13px">Главный офис</td>
                    <td><span class="tag tag-primary">Активен</span></td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/>
                                </svg>
                            </button>
                            <button class="btn-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="role-name">Дмитрий Петренко</span></td>
                    <td style="font-size:13px">Петренко Дмитрий Олегович</td>
                    <td style="color:var(--text-muted);font-size:13px">d.petrenko@realtbank.com.ua</td>
                    <td><span class="role-badge manager">Руководитель офиса</span></td>
                    <td style="font-size:13px">Офис Центр</td>
                    <td><span class="tag tag-primary">Активен</span></td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/>
                                </svg>
                            </button>
                            <button class="btn-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="role-name">Елена Шевченко</span></td>
                    <td style="font-size:13px">Шевченко Елена Викторовна</td>
                    <td style="color:var(--text-muted);font-size:13px">e.shevchenko@realtbank.com.ua</td>
                    <td><span class="role-badge agent">Агент</span></td>
                    <td style="font-size:13px">Офис Центр</td>
                    <td><span class="tag tag-primary">Активен</span></td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/>
                                </svg>
                            </button>
                            <button class="btn-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="role-name">Игорь Бондаренко</span></td>
                    <td style="font-size:13px">Бондаренко Игорь Сергеевич</td>
                    <td style="color:var(--text-muted);font-size:13px">i.bondarenko@realtbank.com.ua</td>
                    <td><span class="role-badge agent">Агент</span></td>
                    <td style="font-size:13px">Офис Приморский</td>
                    <td><span class="tag">Неактивен</span></td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/>
                                </svg>
                            </button>
                            <button class="btn-icon">
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
