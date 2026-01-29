/**
 * Менеджер офисов компании
 * Добавление/удаление офисов динамически
 */
(function() {
    'use strict';

    let officeIndex = 0;

    // Хранилище файлов фотографий для каждого офиса
    const officePhotos = {};

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

        // Делегирование для кнопок удаления офиса и фото
        elements.container.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.btn-remove-office');
            if (removeBtn) {
                const officeItem = removeBtn.closest('.block-offices-item');
                if (officeItem) {
                    removeOffice(officeItem);
                }
                return;
            }

            // Удаление фото
            const photoRemoveBtn = e.target.closest('.photo-remove');
            if (photoRemoveBtn) {
                const thumb = photoRemoveBtn.closest('.office-photo-thumb');
                if (thumb) {
                    removePhoto(thumb);
                }
            }
        });

        // Делегирование для загрузки фото
        elements.container.addEventListener('change', function(e) {
            if (e.target.classList.contains('office-photos-input')) {
                handlePhotoUpload(e.target);
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

        // Инициализируем хранилище фото для этого офиса
        officePhotos[officeIndex] = [];

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
        // Очищаем хранилище фотографий этого офиса
        const officeIdx = officeElement.dataset.officeIndex;
        if (officePhotos[officeIdx]) {
            delete officePhotos[officeIdx];
        }

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

    // ========== Работа с фотографиями офисов ==========

    function handlePhotoUpload(input) {
        const uploadWrapper = input.closest('.office-photos-upload');
        const officeIdx = uploadWrapper.dataset.officeIndex;
        const previewContainer = uploadWrapper.querySelector('.office-photos-preview');

        if (!officePhotos[officeIdx]) {
            officePhotos[officeIdx] = [];
        }

        const files = Array.from(input.files);

        files.forEach((file, index) => {
            if (!file.type.startsWith('image/')) return;

            // Добавляем файл в хранилище
            const photoId = Date.now() + '_' + index;
            officePhotos[officeIdx].push({
                id: photoId,
                file: file
            });

            // Создаём превью
            const reader = new FileReader();
            reader.onload = function(e) {
                const thumb = document.createElement('div');
                thumb.className = 'office-photo-thumb';
                thumb.dataset.photoId = photoId;
                thumb.dataset.officeIndex = officeIdx;
                thumb.innerHTML = `
                    <img src="${e.target.result}" alt="Фото офиса">
                    <button type="button" class="photo-remove" title="Удалить фото">×</button>
                `;
                previewContainer.appendChild(thumb);
            };
            reader.readAsDataURL(file);
        });

        // Обновляем скрытый input с файлами
        updateFileInput(officeIdx, uploadWrapper);

        // Сбрасываем input для возможности повторного выбора тех же файлов
        input.value = '';
    }

    function removePhoto(thumbElement) {
        const photoId = thumbElement.dataset.photoId;
        const officeIdx = thumbElement.dataset.officeIndex;
        const uploadWrapper = thumbElement.closest('.office-photos-upload');

        // Удаляем из хранилища
        if (officePhotos[officeIdx]) {
            officePhotos[officeIdx] = officePhotos[officeIdx].filter(p => p.id !== photoId);
        }

        // Анимация удаления
        thumbElement.style.transition = 'opacity 0.2s, transform 0.2s';
        thumbElement.style.opacity = '0';
        thumbElement.style.transform = 'scale(0.8)';

        setTimeout(() => {
            thumbElement.remove();
            // Обновляем скрытый input
            updateFileInput(officeIdx, uploadWrapper);
        }, 200);
    }

    function updateFileInput(officeIdx, uploadWrapper) {
        // Создаём новый FileList из хранилища
        const dataTransfer = new DataTransfer();

        if (officePhotos[officeIdx]) {
            officePhotos[officeIdx].forEach(photo => {
                dataTransfer.items.add(photo.file);
            });
        }

        // Находим или создаём скрытый input для отправки
        let hiddenInput = uploadWrapper.querySelector('.office-photos-hidden');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'file';
            hiddenInput.name = `offices[${officeIdx}][photos][]`;
            hiddenInput.multiple = true;
            hiddenInput.className = 'office-photos-hidden';
            hiddenInput.style.display = 'none';
            uploadWrapper.appendChild(hiddenInput);
        }

        hiddenInput.files = dataTransfer.files;
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
