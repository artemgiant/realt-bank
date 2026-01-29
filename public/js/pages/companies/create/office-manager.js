/**
 * Менеджер офисов компании
 * Добавление/удаление офисов динамически
 */
(function() {
    'use strict';

    let officeIndex = 0;

    const elements = {
        container: null,
        emptyState: null,
        template: null,
        addBtn: null,
        addFirstBtn: null
    };

    function init() {
        elements.container = document.getElementById('offices-container');
        elements.emptyState = document.getElementById('offices-empty');
        elements.template = document.getElementById('office-template');
        elements.addBtn = document.getElementById('btn-add-office');
        elements.addFirstBtn = document.getElementById('btn-add-first-office');

        if (!elements.container || !elements.template) {
            console.log('Office manager: required elements not found');
            return;
        }

        // Обработчики кнопок добавления
        if (elements.addBtn) {
            elements.addBtn.addEventListener('click', addOffice);
        }
        if (elements.addFirstBtn) {
            elements.addFirstBtn.addEventListener('click', addOffice);
        }

        // Делегирование для кнопок удаления
        elements.container.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.btn-remove-office');
            if (removeBtn) {
                const officeItem = removeBtn.closest('.block-offices-item');
                if (officeItem) {
                    removeOffice(officeItem);
                }
            }
        });

        updateEmptyState();
    }

    function addOffice() {
        const templateContent = elements.template.innerHTML;
        const officeNum = elements.container.children.length + 1;

        // Заменяем плейсхолдеры
        const html = templateContent
            .replace(/__INDEX__/g, officeIndex)
            .replace(/__NUM__/g, officeNum);

        // Создаём элемент
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        const officeElement = wrapper.firstElementChild;

        // Добавляем в контейнер
        elements.container.appendChild(officeElement);

        // Инкрементируем индекс
        officeIndex++;

        // Обновляем состояние
        updateEmptyState();

        // Инициализируем поиск для нового офиса (если есть location-search.js)
        if (typeof window.initLocationSearchForOffice === 'function') {
            window.initLocationSearchForOffice(officeElement);
        }

        // Фокус на название офиса
        const nameInput = officeElement.querySelector('.office-name-input');
        if (nameInput) {
            nameInput.focus();
        }

        // Анимация появления
        officeElement.style.opacity = '0';
        officeElement.style.transform = 'translateY(-10px)';
        requestAnimationFrame(() => {
            officeElement.style.transition = 'opacity 0.3s, transform 0.3s';
            officeElement.style.opacity = '1';
            officeElement.style.transform = 'translateY(0)';
        });
    }

    function removeOffice(officeElement) {
        // Анимация удаления
        officeElement.style.transition = 'opacity 0.2s, transform 0.2s';
        officeElement.style.opacity = '0';
        officeElement.style.transform = 'translateX(-20px)';

        setTimeout(() => {
            officeElement.remove();
            updateOfficeNumbers();
            updateEmptyState();
        }, 200);
    }

    function updateOfficeNumbers() {
        const offices = elements.container.querySelectorAll('.block-offices-item');
        offices.forEach((office, index) => {
            const numSpan = office.querySelector('.office-num');
            if (numSpan) {
                numSpan.textContent = index + 1;
            }
        });
    }

    function updateEmptyState() {
        const hasOffices = elements.container.children.length > 0;

        if (elements.emptyState) {
            elements.emptyState.style.display = hasOffices ? 'none' : 'flex';
        }

        if (elements.addBtn) {
            // Показываем кнопку в заголовке только если есть офисы
            elements.addBtn.style.display = hasOffices ? 'inline-flex' : 'none';
        }
    }

    // Инициализация при загрузке DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Экспорт для внешнего использования
    window.OfficeManager = {
        addOffice: addOffice,
        getOfficeCount: () => elements.container ? elements.container.children.length : 0
    };
})();
