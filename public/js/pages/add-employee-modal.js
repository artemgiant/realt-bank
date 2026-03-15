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
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');

        // Восстановить UI для режима создания
        $('#modal-title').text('Новый сотрудник');
        $('#save-btn-text').text('Сохранить');
        $('#password-field-wrapper').show();
        $('#password').prop('required', true);
    }

    /**
     * Показать ошибки валидации
     */
    function showValidationErrors(errors) {
        // Сброс предыдущих ошибок
        $('#employee-form').find('.is-invalid').removeClass('is-invalid');
        $('#employee-form').find('.invalid-feedback').text('');

        // Показать новые ошибки
        Object.keys(errors).forEach(field => {
            const $input = $(`[name="${field}"]`);
            const $feedback = $(`[data-field="${field}"]`);

            $input.addClass('is-invalid');
            if ($feedback.length) {
                $feedback.text(errors[field][0]).show();
            }
        });
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
                if (typeof toastr !== 'undefined') {
                    toastr.error('Ошибка загрузки данных сотрудника');
                } else {
                    alert('Ошибка загрузки данных сотрудника');
                }
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
        $('#phone').val(employee.phone || '');
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

        // Открыть модалку (данные загрузятся после shown.bs.modal)
        $('#employee-modal').modal('show');

        // Загрузить данные сотрудника с небольшой задержкой чтобы Select2 успел инициализироваться
        setTimeout(function() {
            loadEmployeeData(employeeId);
        }, 200);
    }

    /**
     * Отправка формы
     */
    function submitForm() {
        const $form = $('#employee-form');
        const $submitBtn = $('#save-employee-btn');
        const $btnText = $submitBtn.find('.btn-text');
        const $btnLoader = $submitBtn.find('.btn-loader');

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

                    // Обновить таблицу
                    if (window.employeesTable) {
                        window.employeesTable.ajax.reload();
                    } else {
                        // Попробовать найти таблицу через DataTable API
                        const table = $('#example').DataTable();
                        if (table) {
                            table.ajax.reload();
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
                if (xhr.status === 422) {
                    // Ошибки валидации
                    const errors = xhr.responseJSON.errors;
                    showValidationErrors(errors);
                } else {
                    console.error('Error saving employee:', xhr);
                    const errorMessage = isEditMode ? 'Ошибка при обновлении сотрудника' : 'Ошибка при создании сотрудника';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
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
            setTimeout(initModalComponents, 100);
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
            display: block;
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
    `;
    document.head.appendChild(style);

})(jQuery);
