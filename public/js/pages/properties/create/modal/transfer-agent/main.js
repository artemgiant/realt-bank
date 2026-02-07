/**
 * Модуль передачи объекта другому агенту
 * Управляет модальным окном #transfer-to-agent
 */
(function () {
    'use strict';

    const CONFIG = {
        selectors: {
            modal: '#transfer-to-agent',
            officeSelect: '#transfer-to-agent-office',
            agentSelect: '#transfer-to-agent-name',
            agentName: '#transfer-agent-name',
            agentDescription: '#transfer-agent-description',
            agentTel: '#transfer-agent-tel',
            agentAvatar: '#transfer-agent-avatar',
            whatsapp: '#transfer-whatsapp',
            viber: '#transfer-viber',
            telegram: '#transfer-telegram',
            comment: '#transfer-comment',
            transferBtn: '#transfer-agent-btn',
            // Page agent block
            assignedAgentId: '#assigned-agent-id',
            agentNameDisplay: '#agent-name-display',
            agentCompanyDisplay: '#agent-company-display',
            agentPhoneDisplay: '#agent-phone-display',
            agentAvatarImg: '#agent-avatar-img',
        },
        urls: {
            offices: '/companies/{companyId}/offices',
            searchEmployees: '/employees/ajax-search',
            employeeDetails: '/employees/{id}',
        },
        icons: {
            defaultAvatar: '/img/icon/default-avatar-table.svg',
        }
    };

    let selectedEmployee = null;
    let officesLoaded = false;

    /**
     * Инициализация модуля
     */
    function init() {
        const modal = document.querySelector(CONFIG.selectors.modal);
        if (!modal) return;

        // Слушатели Bootstrap modal events
        modal.addEventListener('show.bs.modal', onModalShow);
        modal.addEventListener('hidden.bs.modal', onModalHidden);

        // Select2 перехватывает change events, поэтому слушаем через jQuery если доступен
        if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
            $(CONFIG.selectors.officeSelect).on('change', function () {
                onOfficeChange({ target: this });
            });
            $(CONFIG.selectors.agentSelect).on('change', function () {
                onAgentChange({ target: this });
            });
        } else {
            var officeSelect = document.querySelector(CONFIG.selectors.officeSelect);
            if (officeSelect) {
                officeSelect.addEventListener('change', onOfficeChange);
            }
            var agentSelect = document.querySelector(CONFIG.selectors.agentSelect);
            if (agentSelect) {
                agentSelect.addEventListener('change', onAgentChange);
            }
        }

        const transferBtn = document.querySelector(CONFIG.selectors.transferBtn);
        if (transferBtn) {
            transferBtn.addEventListener('click', onTransferClick);
        }
    }

    /**
     * При открытии модального окна - загрузить офисы
     */
    function onModalShow() {
        if (!officesLoaded) {
            loadOffices();
        }
        resetAgentInfo();
    }

    /**
     * При закрытии модального окна - сброс
     */
    function onModalHidden() {
        selectedEmployee = null;
        resetAgentInfo();

        const comment = document.querySelector(CONFIG.selectors.comment);
        if (comment) comment.value = '';

        const transferBtn = document.querySelector(CONFIG.selectors.transferBtn);
        if (transferBtn) transferBtn.disabled = true;

        // Сбросить Select2 / native selects
        destroySelect2(CONFIG.selectors.officeSelect);
        destroySelect2(CONFIG.selectors.agentSelect);

        const officeSelect = document.querySelector(CONFIG.selectors.officeSelect);
        const agentSelect = document.querySelector(CONFIG.selectors.agentSelect);

        if (officeSelect) officeSelect.value = '';
        if (agentSelect) {
            agentSelect.innerHTML = '<option value="">Сначала выберите офис</option>';
            agentSelect.disabled = true;
        }

        initSelect2(CONFIG.selectors.officeSelect);
        initSelect2(CONFIG.selectors.agentSelect);
    }

    /**
     * Загрузить офисы компании текущего агента
     */
    function loadOffices() {
        const modal = document.querySelector(CONFIG.selectors.modal);
        const companyId = modal ? modal.dataset.companyId : null;

        if (!companyId) {
            console.warn('TransferAgent: company_id не найден');
            return;
        }

        destroySelect2(CONFIG.selectors.officeSelect);

        const url = CONFIG.urls.offices.replace('{companyId}', companyId);

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
            .then(response => response.json())
            .then(data => {
                const officeSelect = document.querySelector(CONFIG.selectors.officeSelect);
                if (!officeSelect) return;

                officeSelect.innerHTML = '<option value="">Выберите офис</option>';

                const offices = Array.isArray(data) ? data : (data.data || data.offices || []);
                offices.forEach(function (office) {
                    const option = document.createElement('option');
                    option.value = office.id;
                    option.textContent = office.name || ('Офис #' + office.id);
                    officeSelect.appendChild(option);
                });

                officesLoaded = true;

                initSelect2(CONFIG.selectors.officeSelect);
            })
            .catch(function (error) {
                console.error('TransferAgent: Ошибка загрузки офисов', error);
            });
    }

    /**
     * При смене офиса - загрузить сотрудников
     */
    function onOfficeChange(e) {
        const officeId = e.target.value;
        const agentSelect = document.querySelector(CONFIG.selectors.agentSelect);

        if (!officeId) {
            if (agentSelect) {
                agentSelect.innerHTML = '<option value="">Сначала выберите офис</option>';
                agentSelect.disabled = true;
            }
            resetAgentInfo();
            selectedEmployee = null;
            updateTransferBtn();
            return;
        }

        loadEmployees(officeId);
    }

    /**
     * Загрузить сотрудников офиса
     */
    function loadEmployees(officeId) {
        const agentSelect = document.querySelector(CONFIG.selectors.agentSelect);
        if (!agentSelect) return;

        // Сбросить Select2 перед обновлением опций
        destroySelect2(CONFIG.selectors.agentSelect);

        agentSelect.innerHTML = '<option value="">Загрузка...</option>';
        agentSelect.disabled = true;

        const url = CONFIG.urls.searchEmployees + '?office_id=' + officeId;

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
            .then(response => response.json())
            .then(data => {
                agentSelect.innerHTML = '<option value="">Выберите агента</option>';

                const employees = data.results || [];
                employees.forEach(function (emp) {
                    const option = document.createElement('option');
                    option.value = emp.id;
                    option.textContent = emp.text || emp.full_name;
                    agentSelect.appendChild(option);
                });

                agentSelect.disabled = false;

                // Переинициализировать Select2
                initSelect2(CONFIG.selectors.agentSelect);
            })
            .catch(function (error) {
                console.error('TransferAgent: Ошибка загрузки сотрудников', error);
                agentSelect.innerHTML = '<option value="">Ошибка загрузки</option>';
                agentSelect.disabled = false;
                initSelect2(CONFIG.selectors.agentSelect);
            });
    }

    /**
     * Уничтожить Select2 для элемента
     */
    function destroySelect2(selector) {
        try {
            if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
                var $el = $(selector);
                if ($el.data('select2')) {
                    $el.select2('destroy');
                }
            }
        } catch (e) { /* */ }
    }

    /**
     * Инициализировать Select2 для элемента
     */
    function initSelect2(selector) {
        try {
            if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
                $(selector).select2({
                    width: 'resolve',
                    placeholder: 'Выбрать',
                });
                // Перепривязать change event
                $(selector).off('change.transferAgent').on('change.transferAgent', function () {
                    if (selector === CONFIG.selectors.officeSelect) {
                        onOfficeChange({ target: this });
                    } else if (selector === CONFIG.selectors.agentSelect) {
                        onAgentChange({ target: this });
                    }
                });
            }
        } catch (e) { /* */ }
    }

    /**
     * При выборе агента - загрузить его данные
     */
    function onAgentChange(e) {
        const employeeId = e.target.value;

        if (!employeeId) {
            resetAgentInfo();
            selectedEmployee = null;
            updateTransferBtn();
            return;
        }

        loadEmployeeDetails(employeeId);
    }

    /**
     * Загрузить подробности сотрудника
     */
    function loadEmployeeDetails(employeeId) {
        const url = CONFIG.urls.employeeDetails.replace('{id}', employeeId);

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
            .then(response => response.json())
            .then(data => {
                const employee = data.employee || data;
                selectedEmployee = employee;
                displayAgentInfo(employee);
                updateTransferBtn();
            })
            .catch(function (error) {
                console.error('TransferAgent: Ошибка загрузки данных сотрудника', error);
            });
    }

    /**
     * Отобразить информацию об агенте в модальном окне
     */
    function displayAgentInfo(employee) {
        const nameEl = document.querySelector(CONFIG.selectors.agentName);
        const descEl = document.querySelector(CONFIG.selectors.agentDescription);
        const telEl = document.querySelector(CONFIG.selectors.agentTel);
        const avatarEl = document.querySelector(CONFIG.selectors.agentAvatar);

        if (nameEl) nameEl.textContent = employee.full_name || '-';
        if (descEl) descEl.textContent = employee.company_name || employee.position_name || '-';

        if (telEl) {
            if (employee.phone) {
                telEl.textContent = employee.phone;
                telEl.href = 'tel:' + employee.phone;
            } else {
                telEl.textContent = '-';
                telEl.href = 'tel:';
            }
        }

        if (avatarEl) {
            avatarEl.src = employee.photo_url || CONFIG.icons.defaultAvatar;
        }

        // Мессенджеры (скрываем все - у нас нет данных о мессенджерах сотрудника)
        const whatsapp = document.querySelector(CONFIG.selectors.whatsapp);
        const viber = document.querySelector(CONFIG.selectors.viber);
        const telegram = document.querySelector(CONFIG.selectors.telegram);

        if (employee.phone) {
            const cleanPhone = employee.phone.replace(/[^0-9+]/g, '');
            if (whatsapp) {
                whatsapp.href = 'https://wa.me/' + cleanPhone.replace('+', '');
                whatsapp.style.display = '';
            }
            if (viber) {
                viber.href = 'viber://chat?number=' + encodeURIComponent(cleanPhone);
                viber.style.display = '';
            }
            if (telegram) {
                telegram.href = 'https://t.me/' + cleanPhone;
                telegram.style.display = '';
            }
        } else {
            if (whatsapp) whatsapp.style.display = 'none';
            if (viber) viber.style.display = 'none';
            if (telegram) telegram.style.display = 'none';
        }
    }

    /**
     * Сбросить информацию об агенте
     */
    function resetAgentInfo() {
        const nameEl = document.querySelector(CONFIG.selectors.agentName);
        const descEl = document.querySelector(CONFIG.selectors.agentDescription);
        const telEl = document.querySelector(CONFIG.selectors.agentTel);
        const avatarEl = document.querySelector(CONFIG.selectors.agentAvatar);

        if (nameEl) nameEl.textContent = 'Выберите агента';
        if (descEl) descEl.textContent = '-';
        if (telEl) {
            telEl.textContent = '-';
            telEl.href = 'tel:';
        }
        if (avatarEl) avatarEl.src = CONFIG.icons.defaultAvatar;

        const whatsapp = document.querySelector(CONFIG.selectors.whatsapp);
        const viber = document.querySelector(CONFIG.selectors.viber);
        const telegram = document.querySelector(CONFIG.selectors.telegram);
        if (whatsapp) whatsapp.style.display = 'none';
        if (viber) viber.style.display = 'none';
        if (telegram) telegram.style.display = 'none';
    }

    /**
     * Обновить состояние кнопки "Передать"
     */
    function updateTransferBtn() {
        const btn = document.querySelector(CONFIG.selectors.transferBtn);
        if (btn) {
            btn.disabled = !selectedEmployee;
        }
    }

    /**
     * Обработка нажатия кнопки "Передать"
     */
    function onTransferClick() {
        if (!selectedEmployee) return;

        // Обновить скрытое поле с ID агента
        const hiddenInput = document.querySelector(CONFIG.selectors.assignedAgentId);
        if (hiddenInput) {
            hiddenInput.value = selectedEmployee.id;
        }

        // Обновить блок агента на странице
        updatePageAgentBlock(selectedEmployee);

        // Закрыть модальное окно
        const modal = document.querySelector(CONFIG.selectors.modal);
        if (modal) {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        }
    }

    /**
     * Обновить блок агента на основной странице
     */
    function updatePageAgentBlock(employee) {
        const nameDisplay = document.querySelector(CONFIG.selectors.agentNameDisplay);
        const companyDisplay = document.querySelector(CONFIG.selectors.agentCompanyDisplay);
        const phoneDisplay = document.querySelector(CONFIG.selectors.agentPhoneDisplay);
        const avatarImg = document.querySelector(CONFIG.selectors.agentAvatarImg);

        if (nameDisplay) {
            nameDisplay.textContent = employee.full_name || 'Агент';
            nameDisplay.classList.remove('text-muted');
        }

        if (companyDisplay) {
            companyDisplay.textContent = employee.company_name || '';
        }

        if (phoneDisplay) {
            if (employee.phone) {
                phoneDisplay.textContent = employee.phone;
                phoneDisplay.href = 'tel:' + employee.phone;
                phoneDisplay.style.display = '';
            } else {
                phoneDisplay.style.display = 'none';
            }
        }

        if (avatarImg) {
            avatarImg.src = employee.photo_url || CONFIG.icons.defaultAvatar;
        }
    }

    // Инициализация при загрузке DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
