"use strict";

/**
 * Маска для числовых полей
 * Формат: 125 000 (пробел как разделитель тысяч)
 */

(function () {
    document.addEventListener('DOMContentLoaded', function () {

        // Поля с целыми числами
        const integerFields = [
            '#floor',
            '#floors_total',
            '#area_total',
            '#area_living',
            '#area_kitchen',
            '#area_land',
            '#commission'
        ];

        // Поля с десятичными числами (площади) - теперь пусты, так как заказчик хочет только целые числа
        const decimalFields = [];

        // Поля с большими числами (цена)
        const moneyFields = [
            '#price'
        ];

        // Инициализация масок
        integerFields.forEach(function (selector) {
            const input = document.querySelector(selector);
            if (input) {
                initIntegerMask(input);
            }
        });

        decimalFields.forEach(function (selector) {
            const input = document.querySelector(selector);
            if (input) {
                initDecimalMask(input);
            }
        });

        moneyFields.forEach(function (selector) {
            const input = document.querySelector(selector);
            if (input) {
                initMoneyMask(input);
            }
        });

        /**
         * Маска для целых чисел (этажи)
         */
        function initIntegerMask(input) {
            input.addEventListener('input', function (e) {
                let value = this.value.replace(/[^\d]/g, '');
                this.value = formatNumber(value);
            });

            input.addEventListener('keypress', function (e) {
                if (!/[\d]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                    e.preventDefault();
                }
            });

            // Форматируем существующее значение (отбрасываем десятичную часть)
            if (input.value) {
                var clean = input.value.replace(/\s/g, '');
                var intPart = clean.split('.')[0].split(',')[0];
                input.value = formatNumber(intPart.replace(/[^\d]/g, ''));
            }
        }

        /**
         * Маска для десятичных чисел (площади)
         */
        function initDecimalMask(input) {
            input.addEventListener('input', function (e) {
                let value = this.value;

                // Разрешаем только цифры, точку и запятую
                value = value.replace(/[^\d.,]/g, '');

                // Заменяем запятую на точку
                value = value.replace(',', '.');

                // Оставляем только одну точку
                let parts = value.split('.');
                if (parts.length > 2) {
                    value = parts[0] + '.' + parts.slice(1).join('');
                }

                // Ограничиваем до 2 знаков после точки
                if (parts.length === 2 && parts[1].length > 2) {
                    parts[1] = parts[1].substring(0, 2);
                    value = parts[0] + '.' + parts[1];
                }

                // Форматируем целую часть
                if (parts[0]) {
                    parts[0] = formatNumber(parts[0].replace(/\s/g, ''));
                }

                if (parts.length === 2) {
                    this.value = parts[0] + '.' + parts[1];
                } else {
                    this.value = parts[0];
                }
            });

            input.addEventListener('keypress', function (e) {
                if (!/[\d.,]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                    e.preventDefault();
                }
            });

            // Форматируем существующее значение
            if (input.value) {
                let value = input.value.replace(/[^\d.]/g, '');
                let parts = value.split('.');
                parts[0] = formatNumber(parts[0]);
                input.value = parts.join('.');
            }
        }

        /**
         * Маска для денежных полей (цена, комиссия)
         */
        function initMoneyMask(input) {
            input.addEventListener('input', function (e) {
                let value = this.value.replace(/[^\d]/g, '');
                this.value = formatNumber(value);
            });

            input.addEventListener('keypress', function (e) {
                if (!/[\d]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                    e.preventDefault();
                }
            });

            // Форматируем существующее значение (отбрасываем .00 от decimal cast)
            if (input.value) {
                var clean = input.value.replace(/\s/g, '');
                var intPart = clean.split('.')[0].split(',')[0];
                input.value = formatNumber(intPart.replace(/[^\d]/g, ''));
            }
        }

        /**
         * Форматирование числа с пробелами
         * 125000 -> 125 000
         */
        function formatNumber(value) {
            if (!value) return '';

            // Убираем ведущие нули
            value = value.replace(/^0+/, '') || '0';

            // Добавляем пробелы как разделители тысяч
            return value.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }

        /**
         * Получить чистое число из форматированного значения
         * Можно использовать перед отправкой формы
         */
        window.getCleanNumber = function (value) {
            if (!value) return '';
            return value.replace(/\s/g, '');
        };
    });

    // Очистка значений перед отправкой формы
    document.addEventListener('submit', function (e) {
        if (e.target.id === 'property-form') {
            const fieldsToClean = [
                '#price',
                '#area_total',
                '#area_living',
                '#area_kitchen',
                '#area_land',
                '#floor',
                '#floors_total'
            ];

            fieldsToClean.forEach(function (selector) {
                const input = document.querySelector(selector);
                if (input && input.value) {
                    // Убираем пробелы перед отправкой
                    input.value = input.value.replace(/\s/g, '');
                }
            });
        }
    });
})();
