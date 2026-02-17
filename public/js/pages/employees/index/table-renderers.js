/**
 * Функции рендеринга ячеек таблицы сотрудников
 */
window.EmployeeRenderers = {
    /**
     * Рендер чекбокса
     */
    checkbox: function(data, type, row) {
        return `
            <div class="tbody-wrapper checkBox">
                <label class="my-custom-input">
                    <input type="checkbox" class="row-checkbox" data-id="${row.id}">
                    <span class="my-custom-box"></span>
                </label>
            </div>
        `;
    },

    /**
     * Рендер фото сотрудника
     */
    photo: function(data, type, row) {
        const photoUrl = row.photo_url || '/img/default-avatar.jpeg';
        const photoWebp = row.photo_webp || photoUrl;

        return `
            <div class="tbody-wrapper photo">
                <div class="developer-wrapper">
                    <div>
                        <picture>
                            <source srcset="${photoWebp}" type="image/webp">
                            <img src="${photoUrl}" alt="${row.full_name || 'Фото'}">
                        </picture>
                    </div>
                </div>
            </div>
        `;
    },

    /**
     * Рендер информации об агенте (имя, компания, телефон)
     */
    agent: function(data, type, row) {
        const name = row.full_name || 'Без имени';
        const company = row.company_name || '';
        const phone = row.phone || '';
        const phoneFormatted = phone ? `+${phone.replace(/\D/g, '')}` : '';

        return `
            <div class="tbody-wrapper agent">
                <div>
                    <p class="link-name" data-hover-agent data-employee-id="${row.id}">${name}</p>
                    ${company ? `<p data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="${company}">${company}</p>` : ''}
                    ${phone ? `<a href="tel:${phoneFormatted}">${phoneFormatted}</a>` : ''}
                </div>
            </div>
        `;
    },

    /**
     * Рендер должности (Select2)
     */
    position: function(data, type, row) {
        const positions = row.positions || [];
        const currentPosition = row.position_id || '';

        let options = '<option></option>';
        positions.forEach(pos => {
            const selected = pos.id == currentPosition ? 'selected' : '';
            options += `<option value="${pos.id}" ${selected}>${pos.name}</option>`;
        });

        return `
            <div class="tbody-wrapper position">
                <label>
                    <select class="js-example-responsive3 position-select" data-employee-id="${row.id}">
                        ${options}
                    </select>
                </label>
            </div>
        `;
    },

    /**
     * Рендер офиса (Select2)
     */
    office: function(data, type, row) {
        const offices = row.offices || [];
        const currentOffice = row.office_id || '';

        let options = '<option></option>';
        offices.forEach(office => {
            const selected = office.id == currentOffice ? 'selected' : '';
            options += `<option value="${office.id}" ${selected}>${office.name}</option>`;
        });

        return `
            <div class="tbody-wrapper offices">
                <label>
                    <select class="js-example-responsive3 offices-select" data-employee-id="${row.id}">
                        ${options}
                    </select>
                </label>
            </div>
        `;
    },

    /**
     * Рендер количества объектов
     */
    objectsCount: function(data, type, row) {
        const count = row.objects_count || 0;
        return `
            <div class="tbody-wrapper object">
                <p><button class="info-footer-btn btn-others" type="button" data-employee-id="${row.id}">${count}</button></p>
            </div>
        `;
    },

    /**
     * Рендер количества клиентов
     */
    clientsCount: function(data, type, row) {
        const count = row.clients_count || 0;
        return `
            <div class="tbody-wrapper client">
                <p><button class="info-footer-btn btn-others" type="button" data-employee-id="${row.id}">${count}</button></p>
            </div>
        `;
    },

    /**
     * Рендер успешных сделок
     */
    successDeals: function(data, type, row) {
        const count = row.success_deals || 0;
        return `
            <div class="tbody-wrapper succeed">
                <p><button class="info-footer-btn btn-others" type="button" data-employee-id="${row.id}">${count}</button></p>
            </div>
        `;
    },

    /**
     * Рендер неуспешных сделок
     */
    failedDeals: function(data, type, row) {
        const count = row.failed_deals || 0;
        return `
            <div class="tbody-wrapper nosucceed">
                <p><button class="info-footer-btn btn-others" type="button" data-employee-id="${row.id}">${count}</button></p>
            </div>
        `;
    },

    /**
     * Рендер даты "Активный до"
     */
    activeUntil: function(data, type, row) {
        const date = row.active_until || '';
        const time = row.active_until_time || '';

        return `
            <div class="tbody-wrapper activeuntil">
                <p>${time}</p>
                <span>${date}</span>
            </div>
        `;
    },

    /**
     * Рендер действий (меню)
     */
    actions: function(data, type, row) {
        return `
            <div class="tbody-wrapper block-actions">
                <div class="block-actions-wrapper">
                    <div class="menu-burger">
                        <div class="dropdown">
                            <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <picture>
                                    <source srcset="/img/icon/burger-blue.svg" type="image/webp">
                                    <img src="/img/icon/burger-blue.svg" alt="">
                                </picture>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item btn-edit" href="#" data-employee-id="${row.id}">Редактировать</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
};
