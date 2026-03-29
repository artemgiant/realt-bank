"use strict";

/**
 * Интеграция PhotoLoader с формой property-form
 * Этот файл нужно подключить ПОСЛЕ function_on_pages-create.js
 */

(function () {
    // Ждем загрузки DOM
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('#property-form');
        if (!form) return;

        // Перехватываем submit формы
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Очищаем числовые поля от пробелов перед отправкой
            const fieldsToClean = [
                '#price',
                '#commission',
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
                    input.value = input.value.replace(/\s/g, '');
                }
            });

            // Получаем экземпляр PhotoLoader (он должен быть в window.photoLoaderInstance)
            const photoLoader = window.photoLoaderInstance;

            // Создаем FormData из формы
            const formData = new FormData(form);

            // Удаляем старые photos[] если есть (от input[name="loading-photo"])
            formData.delete('loading-photo');

            // Добавляем фото из PhotoLoader
            if (photoLoader && photoLoader.photoArray && photoLoader.photoArray.length > 0) {
                photoLoader.photoArray.forEach((photo, index) => {
                    if (photo.isExisting && photo.serverId) {
                        // Существующие фото - отправляем их ID в порядке следования
                        formData.append('existing_photo_ids[]', photo.serverId);
                    } else if (photo.file) {
                        // Новые фото - отправляем файлы
                        formData.append('photos[]', photo.file, photo.name);
                    }
                });
            }

            // Показываем лоадер
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Сохранение...';
            }

            // Отправляем форму через fetch
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    // Проверяем, есть ли редирект
                    if (response.redirected) {
                        window.location.href = response.url;
                        return null;
                    }

                    // Проверяем content-type
                    const contentType = response.headers.get('content-type');

                    if (contentType && contentType.includes('application/json')) {
                        // Возвращаем JSON вместе со статусом
                        return response.json().then(data => ({
                            data: data,
                            ok: response.ok,
                            status: response.status
                        }));
                    } else {
                        // HTML ответ (редирект или страница с ошибками)
                        return response.text().then(html => {
                            // Если это успешный редирект через meta refresh или JS
                            if (response.ok) {
                                // Пробуем найти URL редиректа в HTML
                                const redirectMatch = html.match(/window\.location\s*=\s*['"]([^'"]+)['"]/);
                                if (redirectMatch) {
                                    window.location.href = redirectMatch[1];
                                    return null;
                                }
                                // Перезагружаем страницу если не нашли редирект
                                window.location.reload();
                                return null;
                            }
                            // Возвращаем HTML для обработки ошибок
                            return { html: html, status: response.status };
                        });
                    }
                })
                .then(result => {
                    if (result === null) return; // Был редирект

                    if (result.html) {
                        // Обработка HTML ответа с ошибками валидации
                        // Можно заменить содержимое страницы или показать ошибки
                        document.open();
                        document.write(result.html);
                        document.close();
                        return;
                    }

                    // JSON ответ
                    if (result.data) {
                        // Проверяем статус ответа
                        if (result.status === 422 && result.data.errors) {
                            // Ошибки валидации Laravel — показываем все ошибки в сводном блоке
                            showValidationErrors(result.data.errors);
                            return;
                        }

                        if (result.ok || result.data.success) {
                            // Успех
                            if (result.data.redirect) {
                                window.location.href = result.data.redirect;
                            } else {
                                window.location.href = '/properties';
                            }
                        } else {
                            // Другая ошибка
                            if (result.data.errors) {
                                showValidationErrors(result.data.errors);
                            } else if (result.data.message) {
                                showGeneralError(result.data.message);
                            } else if (result.data.error) {
                                showGeneralError(result.data.error);
                            } else {
                                showGeneralError('Произошла ошибка при сохранении');
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Ошибка отправки формы:', error);
                    showGeneralError('Произошла ошибка при сохранении. Попробуйте еще раз.');
                })
                .finally(() => {
                    // Восстанавливаем кнопку
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                });
        });
    });

    /**
     * Показать ошибки валидации
     */
    function showValidationErrors(errors) {
        // Удаляем старые ошибки
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        document.querySelectorAll('.alert-validation-error').forEach(el => el.remove());
        document.querySelectorAll('.form-validation-error').forEach(el => el.remove());

        // Собираем все сообщения для сводного блока
        const allMessages = [];
        for (const [field, messages] of Object.entries(errors)) {
            const fieldMessages = Array.isArray(messages) ? messages : [messages];
            fieldMessages.forEach(msg => allMessages.push(msg));

            // Специальная обработка для contact_ids — инпуты существуют только когда есть контакты
            if (field === 'contact_ids') {
                const contactBlock = document.querySelector('#add-contact-block') || document.querySelector('#contacts-list-container');
                if (contactBlock) {
                    contactBlock.classList.add('is-invalid');

                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback d-block';
                    feedback.style.cssText = 'font-size: 14px; margin-top: 8px;';
                    feedback.textContent = fieldMessages.join(', ');

                    contactBlock.parentNode.insertBefore(feedback, contactBlock.nextSibling);
                }
                continue;
            }

            const input = document.querySelector(`[name="${field}"], [name="${field}[]"]`);
            if (input) {
                input.classList.add('is-invalid');

                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback d-block';
                feedback.textContent = fieldMessages.join(', ');

                input.parentNode.appendChild(feedback);
            }
        }

        // Показываем сводный блок ошибок вверху формы (как <x-alerts />)
        if (allMessages.length > 0) {
            const errorList = allMessages.map(msg => `<li>${msg}</li>`).join('');
            const errorBlock = document.createElement('div');
            errorBlock.className = 'form-validation-error';
            errorBlock.innerHTML = `
                <div class="validation-error-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM11 7V13H13V7H11ZM11 15V17H13V15H11Z" fill="white"/>
                    </svg>
                </div>
                <div class="validation-error-content">
                    <strong class="validation-error-title">Пожалуйста, исправьте ошибки:</strong>
                    <ul class="validation-error-list">${errorList}</ul>
                </div>
            `;

            const container = document.querySelector('.create');
            if (container) {
                container.insertBefore(errorBlock, container.firstChild);
                errorBlock.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }

    /**
     * Показать общую ошибку
     */
    function showGeneralError(message) {
        // Удаляем старые алерты
        document.querySelectorAll('.alert-validation-error').forEach(el => el.remove());
        document.querySelectorAll('.form-validation-error').forEach(el => el.remove());

        // Создаем алерт в стиле <x-alerts />
        const errorBlock = document.createElement('div');
        errorBlock.className = 'form-validation-error';
        errorBlock.innerHTML = `
            <div class="validation-error-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM11 7V13H13V7H11ZM11 15V17H13V15H11Z" fill="white"/>
                </svg>
            </div>
            <div class="validation-error-content">
                <strong class="validation-error-title">Ошибка:</strong>
                <ul class="validation-error-list"><li>${message}</li></ul>
            </div>
        `;

        const container = document.querySelector('.create') || document.querySelector('#property-form');
        if (container) {
            container.insertBefore(errorBlock, container.firstChild);
            errorBlock.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
})();
