/**
 * Модуль модалки добавления/редактирования сотрудника
 */
(function ($) {
    'use strict';

    let select2Initialized = false;
    let phoneInputManager = null;
    let isEditMode = false;
    let currentEmployeeId = null;

    /**
     * Форматирование номера телефона с кодом страны (аналог ContactModal.Utils.formatPhoneWithCountryCode)
     */
    function formatPhoneWithCountryCode(phone, inputElement) {
        var digits, countryData, countryCode;

        // Пробуем получить intl-tel-input instance
        var iti = inputElement && (inputElement._iti || $(inputElement).data('iti'));

        if (iti) {
            countryData = iti.getSelectedCountryData();
            countryCode = countryData ? countryData.iso2 : null;
            var dialCode = countryData ? countryData.dialCode : '';
            digits = (phone || '').replace(/\D/g, '');

            // Украинский номер — формат +38 (0XX) XXX-XX-XX
            if (countryCode === 'ua' && digits.length === 9) {
                var areaCode = digits.substring(0, 2);
                var p1 = digits.substring(2, 5);
                var p2 = digits.substring(5, 7);
                var p3 = digits.substring(7, 9);
                return '+38 (0' + areaCode + ') ' + p1 + '-' + p2 + '-' + p3;
            }

            // Для остальных стран — E.164 через intl-tel-input
            var fullNumber = iti.getNumber();
            if (fullNumber) return fullNumber;

            // Fallback: dialCode + digits
            if (dialCode && digits) {
                return '+' + dialCode + digits;
            }
        }

        // Fallback без intl-tel-input
        var cleaned = (phone || '').trim();
        digits = cleaned.replace(/\D/g, '');

        if (cleaned.startsWith('+')) {
            return '+' + digits;
        }

        // Локальный украинский номер
        if (digits.startsWith('0') && digits.length === 10) {
            var sub = digits.substring(1);
            return '+38 (0' + sub.substring(0, 2) + ') ' + sub.substring(2, 5) + '-' + sub.substring(5, 7) + '-' + sub.substring(7, 9);
        }

        return '+380' + digits;
    }

    /**
     * Установка телефона в input с корректным разбором dial code
     * (аналог ContactModal.Form._fillPhones)
     */
    function setPhoneValue(phone) {
        var input = document.querySelector('#employee-modal #phone');
        if (!input || !phone) {
            $('#phone').val(phone);
            return;
        }

        var $input = $(input);
        var phoneClean = (phone + '').trim().replace(/\s/g, '');
        var digits = phoneClean.replace(/\D/g, '');

        // Маппинг dial code -> ISO2
        var dialCodeMap = [
            ['380','ua'],['375','by'],['373','md'],['374','am'],['371','lv'],['370','lt'],['372','ee'],
            ['995','ge'],['994','az'],['993','tm'],['992','tj'],['998','uz'],['996','kg'],['971','ae'],['972','il'],
            ['49','de'],['48','pl'],['47','no'],['46','se'],['45','dk'],['44','gb'],
            ['43','at'],['42','cz'],['41','ch'],['40','ro'],['39','it'],['38','ba'],
            ['36','hu'],['35','pt'],['34','es'],['33','fr'],['32','be'],['31','nl'],['30','gr'],
            ['90','tr'],['86','cn'],['82','kr'],['81','jp'],['61','au'],['55','br'],
            ['1','us'],['7','ru']
        ];

        var countryIso2 = 'ua';
        var dcLen = 0;
        for (var i = 0; i < dialCodeMap.length; i++) {
            if (digits.indexOf(dialCodeMap[i][0]) === 0) {
                countryIso2 = dialCodeMap[i][1];
                dcLen = dialCodeMap[i][0].length;
                break;
            }
        }
        var nationalDigits = dcLen ? digits.slice(dcLen) : digits;

        // Для Украины: если 10 цифр с ведущим 0 — убираем 0 (маска ожидает 9 цифр)
        if (countryIso2 === 'ua' && nationalDigits.length === 10 && nationalDigits.charAt(0) === '0') {
            nationalDigits = nationalDigits.slice(1);
        }

        var countryMasks = {
            'ua': '(00) 000-00-00',
            'ru': '(000) 000-00-00',
            'by': '(00) 000-00-00',
            'kz': '(000) 000-00-00',
            'default': '(000) 000-00-00'
        };

        if (input._iti) {
            var iti = input._iti;
            $input.unmask();
            iti.setCountry(countryIso2);
            input.value = nationalDigits;
            var mask = countryMasks[countryIso2] || countryMasks['default'];
            $input.mask(mask, { clearIfNotMatch: false });
        } else {
            // _iti ещё не инициализирован — ставим только национальные цифры
            // (intl-tel-input покажет dial code отдельно после инициализации)
            $input.unmask();
            input.value = nationalDigits;
            var mask = countryMasks[countryIso2] || countryMasks['default'];
            $input.mask(mask, { clearIfNotMatch: false });
        }
    }

    /**
     * Инициализация PhoneInputManager для телефонов
     */
    function initPhoneInput() {
        // Уничтожаем предыдущий экземпляр если есть
        if (phoneInputManager && typeof phoneInputManager.destroy === 'function') {
            phoneInputManager.destroy();
        }

        if (typeof PhoneInputManager !== 'undefined') {
            phoneInputManager = new PhoneInputManager({
                btnSelector: '.btn-new-tel',
                wrapperSelector: '#employee-modal .item.phone',
                inputClass: 'tel-contact',
                maxPhones: 5,
                initialCountry: 'ua',
                utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js',
                countryMasks: {
                    'ua': '(00) 000-00-00',
                    'ru': '(000) 000-00-00',
                    'by': '(00) 000-00-00',
                    'kz': '(000) 000-00-00',
                    'default': '(000) 000-00-00'
                }
            });
            console.log('PhoneInputManager initialized');
        } else {
            console.warn('PhoneInputManager not found, trying fallback intl-tel-input');
            // Fallback: простая инициализация intl-tel-input без PhoneInputManager
            initSimplePhoneInput();
        }
    }

    /**
     * Простая инициализация intl-tel-input (fallback)
     */
    function initSimplePhoneInput() {
        const phoneInput = document.querySelector('#employee-modal #phone');
        if (phoneInput && typeof intlTelInput !== 'undefined') {
            const iti = intlTelInput(phoneInput, {
                initialCountry: 'ua',
                preferredCountries: ['ua', 'ru', 'by', 'kz', 'pl'],
                separateDialCode: true,
                utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js'
            });
            // Сохраняем instance для возможного уничтожения и получения полного номера
            $(phoneInput).data('iti', iti);
            phoneInput._iti = iti;
            console.log('Simple intl-tel-input initialized');
        }
    }

    /**
     * Инициализация Select2 для модалки
     */
    function initSelect2() {
        if (select2Initialized) return;

        const select2Configs = [
            {
                selector: '#tag_ids',
                options: {
                    dropdownParent: $('#employee-modal'),
                    width: '100%',
                    placeholder: 'Выберите теги',
                    allowClear: true,
                    closeOnSelect: false,
                    language: { noResults: () => "Результатов не найдено" }
                }
            },
            {
                selector: '#position_id',
                options: {
                    dropdownParent: $('#employee-modal'),
                    width: '100%',
                    placeholder: 'Выберите должность',
                    allowClear: true,
                    language: { noResults: () => "Результатов не найдено" }
                }
            },
            {
                selector: '#office_id',
                options: {
                    dropdownParent: $('#employee-modal'),
                    width: '100%',
                    placeholder: 'Выберите офис',
                    allowClear: true,
                    language: { noResults: () => "Результатов не найдено" }
                }
            },
            {
                selector: '#company_id',
                options: {
                    dropdownParent: $('#employee-modal'),
                    width: '100%',
                    placeholder: 'Выберите компанию',
                    allowClear: true,
                    language: { noResults: () => "Результатов не найдено" }
                }
            },
            {
                selector: '#status_id',
                options: {
                    dropdownParent: $('#employee-modal'),
                    width: '100%',
                    placeholder: 'Выберите статус',
                    allowClear: true,
                    language: { noResults: () => "Результатов не найдено" }
                }
            }
        ];

        try {
            select2Configs.forEach(config => {
                if ($(config.selector).length) {
                    if ($(config.selector).data('select2')) {
                        $(config.selector).select2('destroy');
                    }
                    $(config.selector).select2(config.options);
                }
            });

            select2Initialized = true;
            console.log('Employee modal Select2 initialized');
        } catch (error) {
            console.error('Error initializing Select2:', error);
        }
    }

    /**
     * Инициализация DateRangePicker
     */
    function initDatePickers() {
        const datePickerOptions = {
            singleDatePicker: true,
            autoUpdateInput: false,
            locale: {
                format: 'DD.MM.YYYY',
                applyLabel: 'Применить',
                cancelLabel: 'Отмена',
                daysOfWeek: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                firstDay: 1
            },
            drops: 'auto'
        };

        // День рождения
        if ($('#birthday').length && $.fn.daterangepicker) {
            $('#birthday').daterangepicker(datePickerOptions);
            $('#birthday').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD.MM.YYYY'));
            });
            $('#birthday').on('cancel.daterangepicker', function () {
                $(this).val('');
            });
        }

        // Активен до
        if ($('#active_until').length && $.fn.daterangepicker) {
            $('#active_until').daterangepicker(datePickerOptions);
            $('#active_until').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD.MM.YYYY'));
            });
            $('#active_until').on('cancel.daterangepicker', function () {
                $(this).val('');
            });
        }
    }

    /**
     * Превью фото
     */
    function initPhotoPreview() {
        $('#photo').on('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#photo-preview img').attr('src', e.target.result);
                    $('#photo-preview').show();
                };
                reader.readAsDataURL(file);
            }
        });

        $('#remove-photo').on('click', function () {
            $('#photo').val('');
            $('#photo-preview').hide();
            $('#photo-preview img').attr('src', '');
        });
    }

    /**
     * Очистка формы
     */
    function resetForm() {
        const $form = $('#employee-form');
        $form[0].reset();

        // Сброс режима редактирования
        isEditMode = false;
        currentEmployeeId = null;
        $('#employee-id').val('');

        // Сброс Select2
        $('#tag_ids, #position_id, #office_id, #company_id, #status_id').val(null).trigger('change');

        // Сброс превью фото
        $('#photo-preview').hide();
        $('#photo-preview img').attr('src', '');

        // Сброс ошибок валидации
        clearErrors();

        // Восстановить UI для режима создания
        $('#modal-title').text('Новый сотрудник');
        $('#save-btn-text').text('Сохранить');
        $('#password-field-wrapper').show();
        $('#password').prop('required', true);
    }

    /**
     * Скрыть все ошибки
     */
    function clearErrors() {
        $('#employee-form').find('.is-invalid').removeClass('is-invalid');
        $('#employee-form').find('.invalid-feedback').text('').hide();
        $('#employee-errors-container').hide();
        $('#employee-errors-list').empty();

        // Убрать красную рамку с Select2
        $('#employee-form').find('.select2-selection').css('border-color', '');
    }

    /**
     * Показать ошибки валидации (422)
     */
    function showValidationErrors(errors) {
        clearErrors();

        var fieldLabels = {
            'first_name': 'Имя',
            'last_name': 'Фамилия',
            'middle_name': 'Отчество',
            'phone': 'Телефон',
            'email': 'Email',
            'password': 'Пароль',
            'position_id': 'Должность',
            'office_id': 'Офис',
            'company_id': 'Компания',
            'status_id': 'Статус',
            'birthday': 'День рождения',
            'active_until': 'Активен до',
            'tag_ids': 'Теги',
            'comment': 'Комментарий',
            'photo': 'Фото',
            'passport': 'Паспорт',
            'inn': 'ИНН'
        };

        var $errorsList = $('#employee-errors-list');
        var errorCount = 0;

        Object.keys(errors).forEach(function(field) {
            var messages = errors[field];
            var label = fieldLabels[field] || field;

            // Добавить в общий список ошибок
            messages.forEach(function(msg) {
                $errorsList.append('<li>' + label + ': ' + msg + '</li>');
                errorCount++;
            });

            // Подсветка поля
            var $input = $('#employee-form').find('[name="' + field + '"]');
            if (!$input.length) {
                $input = $('#employee-form').find('[name="' + field + '[]"]');
            }
            $input.addClass('is-invalid');

            // Подсветка Select2 контейнера
            var $select2Container = $input.next('.select2-container').find('.select2-selection');
            if ($select2Container.length) {
                $select2Container.css('border-color', '#dc3545');
            }

            // Показать ошибку под полем
            var $feedback = $('#employee-form').find('[data-field="' + field + '"]');
            if ($feedback.length) {
                $feedback.text(messages[0]).show();
            }
        });

        // Показать блок ошибок вверху модалки
        if (errorCount > 0) {
            $('#employee-errors-title').text(
                errorCount === 1
                    ? 'Обнаружена ошибка:'
                    : 'Обнаружено ошибок: ' + errorCount
            );
            $('#employee-errors-container').slideDown(200);

            // Прокрутить к блоку ошибок
            var $modalBody = $('#employee-modal .modal-body');
            $modalBody.animate({ scrollTop: 0 }, 300);
        }
    }

    /**
     * Показать общую ошибку сервера (не 422)
     */
    function showGeneralError(message) {
        clearErrors();
        var $errorsList = $('#employee-errors-list');
        $errorsList.append('<li>' + message + '</li>');
        $('#employee-errors-title').text('Ошибка сервера');
        $('#employee-errors-container').slideDown(200);

        var $modalBody = $('#employee-modal .modal-body');
        $modalBody.animate({ scrollTop: 0 }, 300);
    }

    /**
     * Загрузка данных сотрудника для редактирования
     */
    function loadEmployeeData(employeeId) {
        $.ajax({
            url: `/employees/${employeeId}`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    populateForm(response.employee);
                }
            },
            error: function (xhr) {
                console.error('Error loading employee:', xhr);
                var msg = 'Ошибка загрузки данных сотрудника';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg += ': ' + xhr.responseJSON.message;
                }
                showGeneralError(msg);
            }
        });
    }

    /**
     * Заполнение формы данными сотрудника
     */
    function populateForm(employee) {
        $('#employee-id').val(employee.id);
        $('#first_name').val(employee.first_name || '');
        $('#last_name').val(employee.last_name || '');
        $('#middle_name').val(employee.middle_name || '');
        $('#email').val(employee.email || '');
        setPhoneValue(employee.phone || '');
        $('#passport').val(employee.passport || '');
        $('#inn').val(employee.inn || '');
        $('#comment').val(employee.comment || '');

        // Даты (конвертация Y-m-d в DD.MM.YYYY)
        if (employee.birthday) {
            const parts = employee.birthday.split('-');
            if (parts.length === 3) {
                $('#birthday').val(`${parts[2]}.${parts[1]}.${parts[0]}`);
            }
        }
        if (employee.active_until) {
            const datePart = employee.active_until.split(' ')[0];
            const parts = datePart.split('-');
            if (parts.length === 3) {
                $('#active_until').val(`${parts[2]}.${parts[1]}.${parts[0]}`);
            }
        }

        // Select2 поля (нужно подождать пока Select2 инициализируется)
        setTimeout(function() {
            if (employee.company_id) {
                $('#company_id').val(employee.company_id).trigger('change');
            }
            if (employee.office_id) {
                $('#office_id').val(employee.office_id).trigger('change');
            }
            if (employee.position_id) {
                $('#position_id').val(employee.position_id).trigger('change');
            }
            if (employee.status_id) {
                $('#status_id').val(employee.status_id).trigger('change');
            }
            if (employee.tag_ids && employee.tag_ids.length > 0) {
                $('#tag_ids').val(employee.tag_ids).trigger('change');
            }
        }, 200);

        // Фото превью
        if (employee.photo_url) {
            $('#photo-preview img').attr('src', employee.photo_url);
            $('#photo-preview').show();
        }
    }

    /**
     * Открытие модалки в режиме создания
     */
    function openCreateModal() {
        isEditMode = false;
        currentEmployeeId = null;

        // Установить UI для создания
        $('#modal-title').text('Новый сотрудник');
        $('#save-btn-text').text('Сохранить');
        $('#password-field-wrapper').show();
        $('#password').prop('required', true).attr('placeholder', '');
        $('.password-required-star').show();
        $('#employee-id').val('');

        // Сброс формы
        resetForm();

        // Открыть модалку
        $('#employee-modal').modal('show');
    }

    /**
     * Открытие модалки в режиме редактирования
     */
    function openEditModal(employeeId) {
        isEditMode = true;
        currentEmployeeId = employeeId;

        // Установить UI для редактирования
        $('#modal-title').text('Редактировать сотрудника');
        $('#save-btn-text').text('Сохранить изменения');
        $('#password-field-wrapper').show();
        $('#password').prop('required', false).val('').attr('placeholder', 'Оставьте пустым, чтобы не менять');
        $('.password-required-star').hide();
        $('#employee-id').val(employeeId);

        // Открыть модалку (данные загрузятся после инициализации компонентов)
        $('#employee-modal').modal('show');
    }

    /**
     * Отправка формы
     */
    function submitForm() {
        const $form = $('#employee-form');
        const $submitBtn = $('#save-employee-btn');
        const $btnText = $submitBtn.find('.btn-text');
        const $btnLoader = $submitBtn.find('.btn-loader');

        // Очистить предыдущие ошибки
        clearErrors();

        // Показать loader
        $btnText.addClass('d-none');
        $btnLoader.removeClass('d-none');
        $submitBtn.prop('disabled', true);

        // Собрать данные формы
        const formData = new FormData($form[0]);

        // Получить полный номер телефона с кодом страны из intl-tel-input
        const phoneInput = document.querySelector('#employee-modal #phone');
        if (phoneInput) {
            const fullPhone = formatPhoneWithCountryCode(phoneInput.value, phoneInput);
            if (fullPhone) {
                formData.set('phone', fullPhone);
            }
        }

        // Конвертация дат в формат Y-m-d
        const birthday = $('#birthday').val();
        if (birthday) {
            const parts = birthday.split('.');
            if (parts.length === 3) {
                formData.set('birthday', `${parts[2]}-${parts[1]}-${parts[0]}`);
            }
        }

        const activeUntil = $('#active_until').val();
        if (activeUntil) {
            const parts = activeUntil.split('.');
            if (parts.length === 3) {
                formData.set('active_until', `${parts[2]}-${parts[1]}-${parts[0]}`);
            }
        }

        // Определить URL и метод
        let url = '/employees';
        if (isEditMode && currentEmployeeId) {
            url = `/employees/${currentEmployeeId}`;
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    // Закрыть модалку
                    $('#employee-modal').modal('hide');

                    // Обновить таблицу (false = не сбрасывать страницу)
                    if (window.employeesTable) {
                        window.employeesTable.ajax.reload(null, false);
                    } else {
                        // Попробовать найти таблицу через DataTable API
                        const table = $('#example').DataTable();
                        if (table) {
                            table.ajax.reload(null, false);
                        }
                    }

                    // Показать уведомление
                    const message = response.message || (isEditMode ? 'Сотрудник обновлен' : 'Сотрудник создан');
                    if (typeof toastr !== 'undefined') {
                        toastr.success(message);
                    } else {
                        alert(message);
                    }
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(xhr.responseJSON.errors);
                } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                    showGeneralError(xhr.responseJSON.message);
                } else if (xhr.status === 403) {
                    showGeneralError('У вас нет прав для выполнения этого действия');
                } else if (xhr.status === 500) {
                    showGeneralError('Внутренняя ошибка сервера. Попробуйте позже');
                } else {
                    var errorMessage = isEditMode ? 'Ошибка при обновлении сотрудника' : 'Ошибка при создании сотрудника';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += ': ' + xhr.responseJSON.message;
                    }
                    showGeneralError(errorMessage);
                }
            },
            complete: function () {
                // Скрыть loader
                $btnText.removeClass('d-none');
                $btnLoader.addClass('d-none');
                $submitBtn.prop('disabled', false);
            }
        });
    }

    /**
     * Инициализация компонентов модалки
     */
    function initModalComponents() {
        initSelect2();
        initDatePickers();
        initPhotoPreview();
        initPhoneInput();
    }

    /**
     * Инициализация обработчиков событий
     */
    function initEventHandlers() {
        const $modal = $('#employee-modal');

        // При открытии модалки
        $modal.on('shown.bs.modal', function () {
            initModalComponents();
            // Загружаем данные сотрудника ПОСЛЕ инициализации компонентов (intl-tel-input и т.д.)
            if (isEditMode && currentEmployeeId) {
                setTimeout(function() {
                    loadEmployeeData(currentEmployeeId);
                }, 150);
            }
        });

        // При закрытии модалки
        $modal.on('hidden.bs.modal', function () {
            resetForm();

            // Destroy Select2
            const selectors = ['#tag_ids', '#position_id', '#office_id', '#company_id', '#status_id'];
            selectors.forEach(selector => {
                if ($(selector).data('select2')) {
                    $(selector).select2('destroy');
                }
            });

            // Destroy PhoneInputManager
            if (phoneInputManager && typeof phoneInputManager.destroy === 'function') {
                phoneInputManager.destroy();
                phoneInputManager = null;
            }

            // Destroy simple intl-tel-input fallback
            const $phoneInput = $('#employee-modal #phone');
            if ($phoneInput.data('iti')) {
                $phoneInput.data('iti').destroy();
                $phoneInput.removeData('iti');
            }

            // Удаляем дополнительные поля телефона (если были добавлены)
            $('#employee-modal .item.phone [data-phone-item]').not(':first').remove();

            select2Initialized = false;
        });

        // Отправка формы
        $('#employee-form').on('submit', function (e) {
            e.preventDefault();
            submitForm();
        });

        // Обработчик клика на кнопку редактирования в таблице
        $(document).on('click', '.btn-edit', function (e) {
            e.preventDefault();
            const employeeId = $(this).data('employee-id');
            if (employeeId) {
                openEditModal(employeeId);
            }
        });
    }

    // Экспорт функций глобально
    window.openCreateModal = openCreateModal;
    window.openEditModal = openEditModal;

    // Инициализация при готовности DOM
    $(document).ready(function () {
        initEventHandlers();
        console.log('Employee modal initialized');
    });

    // CSS для Select2 в модалке
    const style = document.createElement('style');
    style.textContent = `
        .select2-container.select2-dropdown {
            z-index: 1060 !important;
        }
        .modal .select2-container--open {
            z-index: 1060 !important;
        }
        .select2-search__field {
            width: 100% !important;
        }
        #employee-form .is-invalid {
            border-color: #dc3545 !important;
        }
        #employee-form .invalid-feedback {
            display: none;
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
        #employee-errors-alert {
            border-radius: 8px;
            font-size: 0.9em;
        }
        #employee-errors-alert ul {
            padding-left: 1.2em;
            list-style-type: disc;
        }
        #employee-errors-alert li {
            margin-bottom: 2px;
        }
    `;
    document.head.appendChild(style);

})(jQuery);
