"use strict";

/**
 * Автозбереження форми в localStorage
 * Зберігає дані при кожній зміні, дозволяє відновити після втрати
 */

(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('property-form');
        if (!form) return;

        // Ключ для localStorage
        const STORAGE_KEY = 'property_form_autosave';

        // Debounce таймер
        let saveTimer = null;
        const SAVE_DELAY = 1000; // 1 секунда після останньої зміни

        // Поля які НЕ зберігаємо
        const EXCLUDED_FIELDS = [
            '_token',
            'loading-photo',
            'documents[]',
            'photos[]'
        ];

        // Створюємо кнопку відновлення
        createRestoreButton();

        // Перевіряємо чи є збережені дані
        checkSavedData();

        // Слухаємо зміни в формі
        initFormListeners();

        // Очищаємо при успішному submit
        form.addEventListener('submit', function() {
            // Очищаємо через невелику затримку (після успішної відправки)
            setTimeout(function() {
                clearSavedData();
            }, 100);
        });

        /**
         * Створити кнопку відновлення
         */
        function createRestoreButton() {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'form-restore-btn';
            button.id = 'form-restore-btn';


            button.innerHTML = `
                <span class="restore-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.33333 10C3.33333 6.31811 6.31811 3.33333 10 3.33333C12.4917 3.33333 14.6667 4.74167 15.7083 6.79167M16.6667 10C16.6667 13.6819 13.6819 16.6667 10 16.6667C7.50833 16.6667 5.33333 15.2583 4.29167 13.2083M15.7083 6.79167V3.33333M15.7083 6.79167H12.25M4.29167 13.2083V16.6667M4.29167 13.2083H7.75" stroke="#3585F5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="restore-text">
                    <span class="restore-label">Відновити</span>
                    <span class="restore-time"></span>
                </span>
            `;

            button.addEventListener('click', function() {
                restoreFormData();
            });

            document.body.appendChild(button);
        }

        /**
         * Ініціалізація слухачів форми
         */
        function initFormListeners() {
            // Текстові поля, textarea
            form.addEventListener('input', function(e) {
                if (shouldSaveField(e.target)) {
                    debouncedSave();
                }
            });

            // Select, checkbox, radio
            form.addEventListener('change', function(e) {
                if (shouldSaveField(e.target)) {
                    debouncedSave();
                }
            });

            // Select2 події
            if (typeof $ !== 'undefined') {
                $(form).find('select').on('select2:select select2:clear', function() {
                    debouncedSave();
                });
            }
        }

        /**
         * Чи потрібно зберігати це поле
         */
        function shouldSaveField(element) {
            if (!element.name) return false;
            if (EXCLUDED_FIELDS.includes(element.name)) return false;
            if (element.type === 'file') return false;
            return true;
        }

        /**
         * Debounced збереження
         */
        function debouncedSave() {
            if (saveTimer) {
                clearTimeout(saveTimer);
            }
            saveTimer = setTimeout(function() {
                saveFormData();
            }, SAVE_DELAY);
        }

        /**
         * Зберегти дані форми
         */
        function saveFormData() {
            const formData = {};
            const elements = form.elements;

            for (let i = 0; i < elements.length; i++) {
                const element = elements[i];

                if (!shouldSaveField(element)) continue;

                const name = element.name;

                // Checkbox
                if (element.type === 'checkbox') {
                    // Для масивів (features[])
                    if (name.endsWith('[]')) {
                        if (!formData[name]) {
                            formData[name] = [];
                        }
                        if (element.checked) {
                            formData[name].push(element.value);
                        }
                    } else {
                        formData[name] = element.checked;
                    }
                }
                // Radio
                else if (element.type === 'radio') {
                    if (element.checked) {
                        formData[name] = element.value;
                    }
                }
                // Інші поля
                else {
                    formData[name] = element.value;
                }
            }

            // Додаємо timestamp
            const saveData = {
                timestamp: Date.now(),
                data: formData
            };

            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(saveData));
                updateButtonState(true, saveData.timestamp);
            } catch (e) {
                console.warn('Помилка збереження в localStorage:', e);
            }
        }

        /**
         * Перевірити наявність збережених даних
         */
        function checkSavedData() {
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (saved) {
                    const saveData = JSON.parse(saved);
                    // Перевіряємо чи дані не старіші 24 годин
                    const maxAge = 24 * 60 * 60 * 1000;
                    if (Date.now() - saveData.timestamp < maxAge) {
                        updateButtonState(true, saveData.timestamp);
                        return true;
                    } else {
                        // Видаляємо застарілі дані
                        clearSavedData();
                    }
                }
            } catch (e) {
                console.warn('Помилка читання localStorage:', e);
            }
            return false;
        }

        /**
         * Відновити дані форми
         */
        function restoreFormData() {
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (!saved) return;

                const saveData = JSON.parse(saved);
                const formData = saveData.data;

                for (const [name, value] of Object.entries(formData)) {
                    // Масиви (checkboxes як features[])
                    if (name.endsWith('[]') && Array.isArray(value)) {
                        const checkboxes = form.querySelectorAll(`[name="${name}"]`);
                        checkboxes.forEach(function(checkbox) {
                            checkbox.checked = value.includes(checkbox.value);
                            // Тригеримо change для оновлення тегів
                            checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                    }
                    // Одиночні checkbox
                    else if (typeof value === 'boolean') {
                        const checkbox = form.querySelector(`[name="${name}"]`);
                        if (checkbox) {
                            checkbox.checked = value;
                        }
                    }
                    // Select та інші поля
                    else {
                        const element = form.querySelector(`[name="${name}"]`);
                        if (element) {
                            element.value = value;

                            // Оновлюємо Select2
                            if (element.tagName === 'SELECT' && typeof $ !== 'undefined') {
                                $(element).trigger('change');
                            }
                        }
                    }
                }

                // Анімація успіху
                showRestoreSuccess();

            } catch (e) {
                console.error('Помилка відновлення даних:', e);
            }
        }

        /**
         * Очистити збережені дані
         */
        function clearSavedData() {
            try {
                localStorage.removeItem(STORAGE_KEY);
                updateButtonState(false);
            } catch (e) {
                console.warn('Помилка очищення localStorage:', e);
            }
        }

        /**
         * Оновити стан кнопки
         */
        function updateButtonState(hasData, timestamp) {
            const button = document.getElementById('form-restore-btn');
            if (!button) return;

            if (hasData) {
                button.classList.add('has-data');

                // Оновлюємо час
                const timeElement = button.querySelector('.restore-time');
                if (timeElement && timestamp) {
                    timeElement.textContent = formatTimeAgo(timestamp);
                }
            } else {
                button.classList.remove('has-data');
            }
        }

        /**
         * Показати успішне відновлення
         */
        function showRestoreSuccess() {
            const button = document.getElementById('form-restore-btn');
            if (!button) return;

            button.classList.add('restored');

            const labelElement = button.querySelector('.restore-label');
            const originalText = labelElement.textContent;
            labelElement.textContent = 'Відновлено!';

            setTimeout(function() {
                button.classList.remove('restored');
                labelElement.textContent = originalText;
                // Ховаємо кнопку після відновлення
                clearSavedData();
            }, 2000);
        }

        /**
         * Форматувати час "X хв тому"
         */
        function formatTimeAgo(timestamp) {
            const now = Date.now();
            const diff = now - timestamp;

            const seconds = Math.floor(diff / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);

            if (seconds < 60) {
                return 'щойно';
            } else if (minutes < 60) {
                return minutes + ' хв тому';
            } else if (hours < 24) {
                return hours + ' год тому';
            } else {
                return 'більше доби тому';
            }
        }

        // Оновлюємо час на кнопці кожну хвилину
        setInterval(function() {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                try {
                    const saveData = JSON.parse(saved);
                    updateButtonState(true, saveData.timestamp);
                } catch (e) {}
            }
        }, 60000);
    });
})();
