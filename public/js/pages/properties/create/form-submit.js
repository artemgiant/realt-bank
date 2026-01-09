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
                    if (photo.file) {
                        formData.append('photos[]', photo.file, photo.name);
                    }
                });

                console.log(`Добавлено ${photoLoader.photoArray.length} фото в форму`);
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
                            // Ошибки валидации Laravel
                            showValidationErrors(result.data.errors);

                            // Показываем общее сообщение если есть
                            if (result.data.message) {
                                showGeneralError(result.data.message);
                            }
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

        // Показываем новые ошибки
        for (const [field, messages] of Object.entries(errors)) {
            const input = document.querySelector(`[name="${field}"], [name="${field}[]"]`);
            if (input) {
                input.classList.add('is-invalid');

                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback d-block';
                feedback.textContent = Array.isArray(messages) ? messages[0] : messages;

                input.parentNode.appendChild(feedback);
            }
        }

        // Скроллим к первой ошибке
        const firstError = document.querySelector('.is-invalid');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    /**
     * Показать общую ошибку
     */
    function showGeneralError(message) {
        // Удаляем старые алерты
        document.querySelectorAll('.alert-validation-error').forEach(el => el.remove());

        // Создаем новый алерт
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show alert-validation-error';
        alert.setAttribute('role', 'alert');
        alert.innerHTML = `
            <strong>Ошибка:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        // Вставляем в начало формы или create контейнера
        const container = document.querySelector('.create') || document.querySelector('#property-form');
        if (container) {
            container.insertBefore(alert, container.firstChild);
            alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
})();
