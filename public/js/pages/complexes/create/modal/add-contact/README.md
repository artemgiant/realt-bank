# Модуль контактов (ContactModal)

## Структура файлов

Скопируйте папку `add-contact` в:
```
public/js/pages/properties/create/modal/add-contact/
```

## Файлы модуля

| Файл | Описание |
|------|----------|
| `config.js` | Конфигурация и константы |
| `utils.js` | Утилиты (debounce, cleanPhoneNumber, getCsrfToken) |
| `components.js` | Инициализация компонентов (PhoneInputManager, Select2, PhotoLoader) |
| `api.js` | Работа с API (searchByPhone, saveContact, show) |
| `form.js` | Работа с формой (fill, clear, индикаторы) |
| `contact-list.js` | Управление списком контактов на странице |
| `handlers.js` | Обработчики событий модалки |
| `main.js` | Главный файл инициализации |

## Подключение в Blade шаблоне

В файле `resources/views/pages/properties/create.blade.php` замените:

```blade
<script src="{{ asset('js/pages/add-contact-modal.js') }}" type="module"></script>
```

На:

```blade
{{-- Модуль контактов (порядок важен!) --}}
<script src="{{ asset('js/pages/properties/create/modal/add-contact/config.js') }}"></script>
<script src="{{ asset('js/pages/properties/create/modal/add-contact/utils.js') }}"></script>
<script src="{{ asset('js/pages/properties/create/modal/add-contact/components.js') }}"></script>
<script src="{{ asset('js/pages/properties/create/modal/add-contact/api.js') }}"></script>
<script src="{{ asset('js/pages/properties/create/modal/add-contact/form.js') }}"></script>
<script src="{{ asset('js/pages/properties/create/modal/add-contact/contact-list.js') }}"></script>
<script src="{{ asset('js/pages/properties/create/modal/add-contact/handlers.js') }}"></script>
<script src="{{ asset('js/pages/properties/create/modal/add-contact/main.js') }}"></script>
```

## Использование

Модуль автоматически инициализируется при загрузке страницы.

Доступ к модулям через глобальный объект:
```javascript
window.ContactModal.Config     // Конфигурация
window.ContactModal.Utils      // Утилиты
window.ContactModal.Components // Компоненты
window.ContactModal.Api        // API методы
window.ContactModal.Form       // Работа с формой
window.ContactModal.ContactList // Список контактов
window.ContactModal.Handlers   // Обработчики событий
```

## Зависимости

- jQuery
- Bootstrap 5 (Modal)
- Select2
- PhoneInputManager (из function_on_pages-create.js)
- PhotoLoaderMini (из function_on_pages-create.js)
