"use strict";

/**
 * Управление тегами особенностей (features)
 * Синхронизация чекбоксов с тегами в #applied-filters
 */

(function() {
    function init() {
        const featuresContainer = document.getElementById('features-menu');
        const tagsContainer = document.getElementById('applied-filters');
        const contactTypeSelect = document.getElementById('contact_type_id');
        const commissionInput = document.getElementById('commission');

        // ID особенности "От посредника"
        const INTERMEDIARY_FEATURE_ID = '136';
        // ID особенности "Комиссия от владельца"
        const OWNER_COMMISSION_FEATURE_ID = '197';

        if (!featuresContainer || !tagsContainer) return;

        // Обработчик открытия/закрытия меню
        const menuBtn = featuresContainer.querySelector('.multiple-menu-btn');
        if (menuBtn) {
            menuBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const currentState = this.getAttribute('data-open-menu');
                const newState = currentState === 'false' ? 'true' : 'false';

                // Закрываем все другие меню
                document.querySelectorAll('.multiple-menu-btn').forEach(function(btn) {
                    if (btn !== menuBtn) {
                        btn.setAttribute('data-open-menu', 'false');
                    }
                });

                // Переключаем текущее меню
                this.setAttribute('data-open-menu', newState);
            });
        }

        // Закрытие меню при клике вне его
        document.addEventListener('click', function(e) {
            if (!featuresContainer.contains(e.target)) {
                if (menuBtn) {
                    menuBtn.setAttribute('data-open-menu', 'false');
                }
            }
        });

        // Обработчик изменения чекбоксов
        featuresContainer.addEventListener('change', function(e) {
            if (e.target.type === 'checkbox' && e.target.name === 'features[]') {
                handleFeatureChange(e.target);
            }
        });

        // Делегирование события удаления тега
        tagsContainer.addEventListener('click', function(e) {
            const closeBtn = e.target.closest('[data-remove-feature]');
            if (closeBtn) {
                const featureId = closeBtn.dataset.removeFeature;
                removeFeatureTag(featureId);
            }
        });

        // Поиск по особенностям
        const searchInput = featuresContainer.querySelector('.multiple-menu-search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                filterFeatures(this.value.toLowerCase());
            });
        }

        // Обработчик изменения типа контакта (Select2)
        if (contactTypeSelect && typeof $ !== 'undefined') {
            // Select2 события
            $(contactTypeSelect).on('select2:select', function(e) {
                handleContactTypeChange(e.params.data.text);
            });

            $(contactTypeSelect).on('select2:clear', function(e) {
                handleContactTypeChange('');
            });

            // Проверяем начальное значение при загрузке страницы
            setTimeout(function() {
                const selectedOption = contactTypeSelect.options[contactTypeSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    handleContactTypeChange(selectedOption.text);
                }
            }, 100);
        }

        // Обработчик изменения поля комиссии
        if (commissionInput) {
            commissionInput.addEventListener('input', function() {
                handleCommissionChange(this.value);
            });

            // Проверяем начальное значение при загрузке страницы
            setTimeout(function() {
                if (commissionInput.value) {
                    handleCommissionChange(commissionInput.value);
                }
            }, 100);
        }

        // Инициализация тегов для уже выбранных чекбоксов (при редактировании)
        initExistingFeatures();

        /**
         * Обработка изменения поля комиссии
         */
        function handleCommissionChange(value) {
            // Очищаем значение от пробелов и проверяем
            const cleanValue = (value || '').replace(/\s/g, '').trim();
            const hasCommission = cleanValue.length > 0 && cleanValue !== '0';

            const checkbox = featuresContainer.querySelector(`input[name="features[]"][value="${OWNER_COMMISSION_FEATURE_ID}"]`);
            if (!checkbox) return;

            if (hasCommission) {
                // Выбираем "Комиссия от владельца"
                if (!checkbox.checked) {
                    checkbox.checked = true;
                    const featureName = checkbox.closest('.my-custom-input').querySelector('.my-custom-text').textContent;
                    addFeatureTag(OWNER_COMMISSION_FEATURE_ID, featureName);
                }
            } else {
                // Убираем "Комиссия от владельца"
                if (checkbox.checked) {
                    checkbox.checked = false;
                    removeFeatureTagElement(OWNER_COMMISSION_FEATURE_ID);
                }
            }
        }

        /**
         * Обработка изменения типа контакта
         */
        function handleContactTypeChange(selectedText) {
            const text = (selectedText || '').trim().toLowerCase();

            // Проверяем, выбран ли "Агент"
            const isAgent = text === 'агент';

            const checkbox = featuresContainer.querySelector(`input[name="features[]"][value="${INTERMEDIARY_FEATURE_ID}"]`);
            if (!checkbox) return;

            if (isAgent) {
                // Выбираем "От посредника"
                if (!checkbox.checked) {
                    checkbox.checked = true;
                    const featureName = checkbox.closest('.my-custom-input').querySelector('.my-custom-text').textContent;
                    addFeatureTag(INTERMEDIARY_FEATURE_ID, featureName);
                }
            } else {
                // Убираем "От посредника"
                if (checkbox.checked) {
                    checkbox.checked = false;
                    removeFeatureTagElement(INTERMEDIARY_FEATURE_ID);
                }
            }
        }

        /**
         * Обработка изменения чекбокса
         */
        function handleFeatureChange(checkbox) {
            const featureId = checkbox.value;
            const featureName = checkbox.closest('.my-custom-input').querySelector('.my-custom-text').textContent;

            if (checkbox.checked) {
                addFeatureTag(featureId, featureName);
            } else {
                removeFeatureTagElement(featureId);
            }
        }

        /**
         * Добавить тег особенности
         */
        function addFeatureTag(featureId, featureName) {
            // Проверяем, нет ли уже такого тега
            if (tagsContainer.querySelector(`[data-feature-id="${featureId}"]`)) {
                return;
            }

            const tag = document.createElement('div');
            tag.className = 'badge rounded-pill';
            tag.setAttribute('data-feature-id', featureId);
            tag.innerHTML = `
                ${featureName}
                <button type="button" aria-label="Close" data-remove-feature="${featureId}">
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.878207 9.87891C0.653517 9.87891 0.428717 9.79318 0.257263 9.62173C-0.0857544 9.27871 -0.0857544 8.72285 0.257263 8.37984L8.37992 0.257181C8.72294 -0.085727 9.27879 -0.085727 9.62181 0.257181C9.96472 0.600089 9.96472 1.15605 9.62181 1.49896L1.49915 9.62162C1.3277 9.79318 1.1029 9.87891 0.878207 9.87891Z" fill="#AAAAAA"></path>
                        <path d="M9.00086 9.8788C8.77606 9.8788 8.55137 9.79307 8.37992 9.62162L0.257263 1.49896C-0.0857544 1.15605 -0.0857544 0.600089 0.257263 0.257181C0.600171 -0.085727 1.15613 -0.085727 1.49904 0.257181L9.6217 8.37984C9.96461 8.72285 9.96461 9.27871 9.6217 9.62173C9.45035 9.79307 9.22566 9.8788 9.00086 9.8788Z" fill="#AAAAAA"></path>
                    </svg>
                </button>
            `;

            tagsContainer.appendChild(tag);
        }

        /**
         * Удалить тег и снять чекбокс
         */
        function removeFeatureTag(featureId) {
            // Снимаем чекбокс
            const checkbox = featuresContainer.querySelector(`input[name="features[]"][value="${featureId}"]`);
            if (checkbox) {
                checkbox.checked = false;
            }

            // Удаляем тег
            removeFeatureTagElement(featureId);
        }

        /**
         * Удалить только элемент тега
         */
        function removeFeatureTagElement(featureId) {
            const tag = tagsContainer.querySelector(`[data-feature-id="${featureId}"]`);
            if (tag) {
                tag.remove();
            }
        }

        /**
         * Фильтрация особенностей по поиску
         */
        function filterFeatures(searchText) {
            const items = featuresContainer.querySelectorAll('.multiple-menu-item');

            items.forEach(function(item) {
                const text = item.querySelector('.my-custom-text').textContent.toLowerCase();
                if (text.includes(searchText)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        /**
         * Инициализация тегов для уже выбранных чекбоксов
         */
        function initExistingFeatures() {
            const checkedBoxes = featuresContainer.querySelectorAll('input[name="features[]"]:checked');

            checkedBoxes.forEach(function(checkbox) {
                const featureId = checkbox.value;
                const featureName = checkbox.closest('.my-custom-input').querySelector('.my-custom-text').textContent;
                addFeatureTag(featureId, featureName);
            });
        }
    }

    // Инициализация: если DOM уже готов, запускаем сразу, иначе ждём события
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
