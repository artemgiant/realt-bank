/**
 * Модуль модалки добавления сотрудника
 */
(function($) {
    'use strict';

    let select2Initialized = false;

    /**
     * Инициализация Select2 для модалки
     */
    function initSelect2() {
        if (select2Initialized) return;

        const select2Configs = [
            {
                selector: '#tag_ids',
                options: {
                    dropdownParent: $('#add-employee-modal'),
                    width: '100%',
                    placeholder: 'Выберите теги',
                    allowClear: true,
                    language: { noResults: () => "Результатов не найдено" }
                }
            },
            {
                selector: '#position_id',
                options: {
                    dropdownParent: $('#add-employee-modal'),
                    width: '100%',
                    placeholder: 'Выберите должность',
                    allowClear: true,
                    language: { noResults: () => "Результатов не найдено" }
                }
            },
            {
                selector: '#office_id',
                options: {
                    dropdownParent: $('#add-employee-modal'),
                    width: '100%',
                    placeholder: 'Выберите офис',
                    allowClear: true,
                    language: { noResults: () => "Результатов не найдено" }
                }
            },
            {
                selector: '#company_id',
                options: {
                    dropdownParent: $('#add-employee-modal'),
                    width: '100%',
                    placeholder: 'Выберите компанию',
                    allowClear: true,
                    language: { noResults: () => "Результатов не найдено" }
                }
            },
            {
                selector: '#status_id',
                options: {
                    dropdownParent: $('#add-employee-modal'),
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
            $('#birthday').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD.MM.YYYY'));
            });
            $('#birthday').on('cancel.daterangepicker', function() {
                $(this).val('');
            });
        }

        // Активен до
        if ($('#active_until').length && $.fn.daterangepicker) {
            $('#active_until').daterangepicker(datePickerOptions);
            $('#active_until').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD.MM.YYYY'));
            });
            $('#active_until').on('cancel.daterangepicker', function() {
                $(this).val('');
            });
        }
    }

    /**
     * Превью фото
     */
    function initPhotoPreview() {
        $('#photo').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#photo-preview img').attr('src', e.target.result);
                    $('#photo-preview').show();
                };
                reader.readAsDataURL(file);
            }
        });

        $('#remove-photo').on('click', function() {
            $('#photo').val('');
            $('#photo-preview').hide();
            $('#photo-preview img').attr('src', '');
        });
    }

    /**
     * Очистка формы
     */
    function resetForm() {
        const $form = $('#add-employee-form');
        $form[0].reset();

        // Сброс Select2
        $('#tag_ids, #position_id, #office_id, #company_id, #status_id').val(null).trigger('change');

        // Сброс превью фото
        $('#photo-preview').hide();
        $('#photo-preview img').attr('src', '');

        // Сброс ошибок валидации
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');
    }

    /**
     * Показать ошибки валидации
     */
    function showValidationErrors(errors) {
        // Сброс предыдущих ошибок
        $('#add-employee-form').find('.is-invalid').removeClass('is-invalid');
        $('#add-employee-form').find('.invalid-feedback').text('');

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
     * Отправка формы
     */
    function submitForm() {
        const $form = $('#add-employee-form');
        const $submitBtn = $('#save-employee-btn');
        const $btnText = $submitBtn.find('.btn-text');
        const $btnLoader = $submitBtn.find('.btn-loader');

        // Показать loader
        $btnText.addClass('d-none');
        $btnLoader.removeClass('d-none');
        $submitBtn.prop('disabled', true);

        // Собрать данные формы
        const formData = new FormData($form[0]);

        // Конвертация дат в формат Y-m-d
        const birthday = $('#birthday').val();
        if (birthday) {
            const parts = birthday.split('.');
            formData.set('birthday', `${parts[2]}-${parts[1]}-${parts[0]}`);
        }

        const activeUntil = $('#active_until').val();
        if (activeUntil) {
            const parts = activeUntil.split('.');
            formData.set('active_until', `${parts[2]}-${parts[1]}-${parts[0]}`);
        }

        $.ajax({
            url: '/employees',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Закрыть модалку
                    $('#add-employee-modal').modal('hide');

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
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Сотрудник создан');
                    } else {
                        alert(response.message || 'Сотрудник создан');
                    }
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Ошибки валидации
                    const errors = xhr.responseJSON.errors;
                    showValidationErrors(errors);
                } else {
                    console.error('Error creating employee:', xhr);
                    alert('Ошибка при создании сотрудника');
                }
            },
            complete: function() {
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
    }

    /**
     * Инициализация обработчиков событий
     */
    function initEventHandlers() {
        const $modal = $('#add-employee-modal');

        // При открытии модалки
        $modal.on('shown.bs.modal', function() {
            setTimeout(initModalComponents, 100);
        });

        // При закрытии модалки
        $modal.on('hidden.bs.modal', function() {
            resetForm();

            // Destroy Select2
            const selectors = ['#tag_ids', '#position_id', '#office_id', '#company_id', '#status_id'];
            selectors.forEach(selector => {
                if ($(selector).data('select2')) {
                    $(selector).select2('destroy');
                }
            });

            select2Initialized = false;
        });

        // Отправка формы
        $('#add-employee-form').on('submit', function(e) {
            e.preventDefault();
            submitForm();
        });
    }

    // Инициализация при готовности DOM
    $(document).ready(function() {
        initEventHandlers();
        console.log('Add employee modal initialized');
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
        #add-employee-form .is-invalid {
            border-color: #dc3545 !important;
        }
        #add-employee-form .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
    `;
    document.head.appendChild(style);

})(jQuery);
