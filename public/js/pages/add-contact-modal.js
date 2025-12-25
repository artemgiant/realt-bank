/**
 * Модуль для работы с модальным окном контакта
 * Поиск по телефону, создание/редактирование, привязка к объекту
 */
import {PhotoLoaderMini, PhoneInputManager} from "./function_on_pages-create.js";

// ========== Конфигурация ==========
const CONFIG = {
	debounceDelay: 600,
	searchMinLength: 6,
	urls: {
		searchByPhone: '/contacts/ajax-search-by-phone',
		store: '/contacts/ajax-store',
		show: '/contacts/{id}/ajax'
	}
};

// ========== Состояние ==========
let phoneManager = null;
let select2Initialized = false;
let currentContactId = null; // ID найденного/редактируемого контакта
let isEditMode = false;
let debounceTimer = null;

// ========== Утилиты ==========

/**
 * Debounce функция
 */
function debounce(func, wait) {
	return function(...args) {
		clearTimeout(debounceTimer);
		debounceTimer = setTimeout(() => func.apply(this, args), wait);
	};
}

/**
 * Очистка номера телефона от лишних символов
 */
function cleanPhoneNumber(phone) {
	return phone.replace(/[^0-9+]/g, '');
}

/**
 * Получение CSRF токена
 */
function getCsrfToken() {
	return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

// ========== Инициализация компонентов ==========

/**
 * Инициализация PhoneInputManager
 */
function initPhoneInputManager() {
	if (phoneManager) return;

	const btnSelector = '.btn-new-tel';
	const wrapperSelector = '#add-contact-modal .modal-row .item.phone';

	if (document.querySelector(btnSelector)) {
		try {
			phoneManager = new PhoneInputManager({
				btnSelector: btnSelector,
				wrapperSelector: wrapperSelector,
				inputClass: 'tel-contact',
				maxPhones: 5,
				initialCountry: 'ua',
				utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js',
				countryMasks: {
					'ua': '(99) 999-99-99',
					'us': '(999) 999-9999',
					'gb': '9999 999999',
					'de': '999 99999999',
					'fr': '9 99-99-99-99',
					'pl': '999 999-999',
					'it': '999 999-9999',
					'es': '999 99-99-99',
					'default': '(999) 999-99-99'
				}
			});
		} catch (error) {
			console.error('Error initializing PhoneInputManager:', error);
		}
	}
}

/**
 * Инициализация Select2
 */
function initSelect2() {
	if (select2Initialized) return;

	const select2Configs = [
		{
			selector: '#tags-client-modal',
			options: {
				dropdownParent: $('#add-contact-modal'),
				width: '100%',
				placeholder: 'Выберите тег',
				allowClear: true,
				language: { noResults: () => "Результатов не найдено" }
			}
		},
		{
			selector: '#type-contact-modal',
			options: {
				dropdownParent: $('#add-contact-modal'),
				width: '100%',
				placeholder: 'Выберите тип',
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

				$(config.selector).on('select2:open', function() {
					setTimeout(() => {
						const searchField = document.querySelector('.select2-search__field');
						if (searchField) searchField.focus();
					}, 100);
				});
			}
		});

		select2Initialized = true;
	} catch (error) {
		console.error('Error initializing Select2:', error);
	}
}

/**
 * Инициализация PhotoLoader
 */
function initPhotoLoader() {
	const modalElement = document.getElementById('add-contact-modal');
	if (modalElement) {
		try {
			new PhotoLoaderMini({
				inputIdSelector: '#loading-photo-contact-modal',
				wrapperClassSelector: '.photo-info-list',
				context: modalElement
			});
		} catch (error) {
			console.error('Error initializing PhotoLoaderMini:', error);
		}
	}
}

// ========== Поиск по телефону ==========

/**
 * Поиск контакта по номеру телефона
 */
async function searchByPhone(phone) {
	const cleanPhone = cleanPhoneNumber(phone);

	if (cleanPhone.length < CONFIG.searchMinLength) {
		hideFoundIndicator();
		return;
	}

	try {
		const response = await fetch(`${CONFIG.urls.searchByPhone}?phone=${encodeURIComponent(phone)}`, {
			method: 'GET',
			headers: {
				'Accept': 'application/json',
				'X-Requested-With': 'XMLHttpRequest'
			}
		});

		const data = await response.json();

		if (data.success && data.found) {
			fillFormWithContact(data.contact);
			showFoundIndicator();
			currentContactId = data.contact.id;
		} else {
			hideFoundIndicator();
			currentContactId = null;
		}
	} catch (error) {
		console.error('Error searching contact by phone:', error);
		hideFoundIndicator();
	}
}

/**
 * Debounced поиск
 */
const debouncedSearch = debounce(searchByPhone, CONFIG.debounceDelay);

// ========== Заполнение формы ==========

/**
 * Заполнение формы данными контакта
 */
function fillFormWithContact(contact) {
	const form = document.getElementById('contact-modal-form');
	if (!form) return;

	// Устанавливаем ID контакта
	const contactIdInput = document.getElementById('contact-id-modal');
	if (contactIdInput) contactIdInput.value = contact.id;

	// Заполняем текстовые поля
	setInputValue('#first-name-contact-modal', contact.first_name);
	setInputValue('#last-name-contact-modal', contact.last_name);
	setInputValue('#middle-name-contact-modal', contact.middle_name);
	setInputValue('#email-contact-modal', contact.email);
	setInputValue('#comment-contact-modal', contact.comment);
	setInputValue('#telegram-contact-modal', contact.telegram);
	setInputValue('#viber-contact-modal', contact.viber);
	setInputValue('#whatsapp-contact-modal', contact.whatsapp);
	setInputValue('#passport-contact-modal', contact.passport);
	setInputValue('#inn-contact-modal', contact.inn);

	// Заполняем select2
	if (contact.contact_type) {
		$('#type-contact-modal').val(contact.contact_type).trigger('change');
	}
	if (contact.tags) {
		$('#tags-client-modal').val(contact.tags).trigger('change');
	}
}

/**
 * Установка значения input
 */
function setInputValue(selector, value) {
	const input = document.querySelector(selector);
	if (input) input.value = value || '';
}

/**
 * Очистка формы
 */
function clearForm() {
	const form = document.getElementById('contact-modal-form');
	if (form) form.reset();

	// Очищаем hidden поля
	const contactIdInput = document.getElementById('contact-id-modal');
	if (contactIdInput) contactIdInput.value = '';

	// Сбрасываем select2
	$('#type-contact-modal').val('').trigger('change');
	$('#tags-client-modal').val('').trigger('change');

	// Скрываем индикатор
	hideFoundIndicator();

	// Сбрасываем состояние
	currentContactId = null;
	isEditMode = false;
}

// ========== Индикатор найденного контакта ==========

function showFoundIndicator() {
	const indicator = document.getElementById('contact-found-indicator');
	if (indicator) indicator.classList.remove('d-none');
}

function hideFoundIndicator() {
	const indicator = document.getElementById('contact-found-indicator');
	if (indicator) indicator.classList.add('d-none');
}

// ========== Сохранение контакта ==========

/**
 * Сохранение контакта через AJAX
 */
async function saveContact(form) {
	const formData = new FormData(form);

	// Собираем телефоны
	const phones = [];
	document.querySelectorAll('#add-contact-modal .tel-contact').forEach((input, index) => {
		const phone = input.value.trim();
		if (phone) {
			// Добавляем +380 если номер не начинается с +
			let fullPhone = phone;
			if (!phone.startsWith('+')) {
				fullPhone = '+380' + phone.replace(/^0/, ''); // убираем ведущий 0 если есть
				console.log(fullPhone);
			}
			phones.push({
				phone: fullPhone,
				is_primary: index === 0
			});
		}
	});

	// Удаляем старые поля телефонов и добавляем новые
	for (let key of formData.keys()) {
		if (key.startsWith('phones[')) {
			formData.delete(key);
		}
	}
	phones.forEach((phone, index) => {
		formData.append(`phones[${index}][phone]`, phone.phone);
		formData.append(`phones[${index}][is_primary]`, phone.is_primary ? '1' : '0');
	});

	// Если контакт найден - возвращаем его данные без создания нового
	if (currentContactId) {
		return {
			success: true,
			isExisting: true,
			contact: {
				id: currentContactId,
				full_name: (document.getElementById('last-name-contact-modal')?.value || '') + ' ' +
					(document.getElementById('first-name-contact-modal')?.value || ''),
				primary_phone: phones[0]?.phone || '',
				contact_type: document.getElementById('type-contact-modal')?.value,
				contact_type_name: getContactTypeName(document.getElementById('type-contact-modal')?.value),
				messengers: getMessengersFromForm(),
				telegram: document.getElementById('telegram-contact-modal')?.value,
				viber: document.getElementById('viber-contact-modal')?.value,
				whatsapp: document.getElementById('whatsapp-contact-modal')?.value
			}
		};
	}

	// Создаем нового контакта
	try {
		const response = await fetch(CONFIG.urls.store, {
			method: 'POST',
			headers: {
				'Accept': 'application/json',
				'X-Requested-With': 'XMLHttpRequest',
				'X-CSRF-TOKEN': getCsrfToken()
			},
			body: formData
		});

		const data = await response.json();

		if (!response.ok) {
			throw { response: data, status: response.status };
		}

		return { success: true, isExisting: false, contact: data.contact };
	} catch (error) {
		console.error('Error saving contact:', error);
		throw error;
	}
}

/**
 * Получение названия типа контакта
 */
function getContactTypeName(type) {
	const types = {
		'owner': 'Владелец',
		'agent': 'Агент',
		'developer': 'Девелопер'
	};
	return types[type] || '-';
}

/**
 * Получение мессенджеров из формы
 */
function getMessengersFromForm() {
	const messengers = [];
	if (document.getElementById('whatsapp-contact-modal')?.value) messengers.push('whatsapp');
	if (document.getElementById('viber-contact-modal')?.value) messengers.push('viber');
	if (document.getElementById('telegram-contact-modal')?.value) messengers.push('telegram');
	return messengers;
}

// ========== Управление списком контактов на странице ==========

/**
 * Добавление контакта в список на странице
 */
function addContactToPage(contact) {
	const container = document.getElementById('contacts-list-container');
	const addBlock = document.getElementById('add-contact-block');
	const addMoreBtn = document.getElementById('add-more-contact-btn');
	const template = document.getElementById('contact-card-template');

	if (!container || !template) return;

	// Проверяем, не добавлен ли уже этот контакт
	if (container.querySelector(`[data-contact-id="${contact.id}"]`)) {
		alert('Этот контакт уже добавлен');
		return false;
	}

	// Клонируем шаблон
	const clone = template.content.cloneNode(true);
	const card = clone.querySelector('.contact-card');

	// Заполняем данными
	card.setAttribute('data-contact-id', contact.id);
	card.querySelector('.contact-name').textContent = contact.full_name || '-';
	card.querySelector('.contact-type').textContent = contact.contact_type_name || '-';

	const phoneLink = card.querySelector('.contact-phone');
	if (phoneLink && contact.primary_phone) {
		phoneLink.href = 'tel:' + contact.primary_phone.replace(/[^0-9+]/g, '');
		phoneLink.textContent = contact.primary_phone;
	}

	// Аватар
	if (contact.photo_url) {
		card.querySelector('.contact-avatar').src = contact.photo_url;
	}

	// Мессенджеры
	const messengersContainer = card.querySelector('.contact-messengers');
	if (messengersContainer && contact.messengers) {
		messengersContainer.innerHTML = buildMessengersHtml(contact);
	}

	// Hidden input для формы
	card.querySelector('.contact-id-input').value = contact.id;

	// Добавляем карточку
	container.appendChild(clone);

	// Скрываем блок "добавить" и показываем кнопку "добавить еще"
	if (addBlock) addBlock.classList.add('d-none');
	if (addMoreBtn) addMoreBtn.classList.remove('d-none');

	return true;
}

/**
 * Построение HTML мессенджеров
 */
function buildMessengersHtml(contact) {
	let html = '';

	if (contact.whatsapp || (contact.messengers && contact.messengers.includes('whatsapp'))) {
		const link = contact.whatsapp_link || contact.whatsapp || '#';
		html += `<a href="${link}" target="_blank">
            <picture><source srcset="/img/icon/icon-table/cnapchat.svg" type="image/webp"><img src="/img/icon/icon-table/cnapchat.svg" alt="WhatsApp"></picture>
        </a>`;
	}

	if (contact.viber || (contact.messengers && contact.messengers.includes('viber'))) {
		const link = contact.viber_link || contact.viber || '#';
		html += `<a href="${link}" target="_blank">
            <picture><source srcset="/img/icon/icon-table/viber.svg" type="image/webp"><img src="/img/icon/icon-table/viber.svg" alt="Viber"></picture>
        </a>`;
	}

	if (contact.telegram || (contact.messengers && contact.messengers.includes('telegram'))) {
		const link = contact.telegram_link || contact.telegram || '#';
		html += `<a href="${link}" target="_blank">
            <picture><source srcset="/img/icon/icon-table/tg.svg" type="image/webp"><img src="/img/icon/icon-table/tg.svg" alt="Telegram"></picture>
        </a>`;
	}

	return html;
}

/**
 * Удаление контакта из списка
 */
function removeContactFromPage(contactId) {
	const container = document.getElementById('contacts-list-container');
	const addBlock = document.getElementById('add-contact-block');
	const addMoreBtn = document.getElementById('add-more-contact-btn');

	const card = container?.querySelector(`[data-contact-id="${contactId}"]`);
	if (card) card.remove();

	// Если контактов больше нет - показываем блок "добавить" и скрываем кнопку
	const remainingCards = container?.querySelectorAll('.contact-card');
	if (!remainingCards || remainingCards.length === 0) {
		if (addBlock) addBlock.classList.remove('d-none');
		if (addMoreBtn) addMoreBtn.classList.add('d-none');
	}
}

// ========== Обработчики событий ==========

/**
 * Инициализация обработчиков модального окна
 */
function initModalHandlers() {
	const modal = document.getElementById('add-contact-modal');
	if (!modal) return;

	// При открытии модалки
	modal.addEventListener('shown.bs.modal', function() {
		setTimeout(() => {
			initPhoneInputManager();
			initSelect2();
			initPhotoLoader();

			// Если не режим редактирования - очищаем форму
			if (!isEditMode) {
				clearForm();
			}
		}, 300);
	});

	// При закрытии модалки
	modal.addEventListener('hidden.bs.modal', function() {
		// Очищаем PhoneInputManager
		if (phoneManager && typeof phoneManager.destroy === 'function') {
			phoneManager.destroy();
			phoneManager = null;
		}

		// Очищаем Select2
		['#tags-client-modal', '#type-contact-modal'].forEach(selector => {
			if ($(selector).data('select2')) {
				$(selector).select2('destroy');
			}
		});

		select2Initialized = false;
		isEditMode = false;
	});
}

/**
 * Инициализация поиска по телефону
 */
function initPhoneSearch() {
	document.addEventListener('input', function(e) {
		if (e.target.matches('#add-contact-modal .tel-contact')) {
			// Только для первого телефона делаем поиск
			const firstPhoneInput = document.querySelector('#add-contact-modal .tel-contact');
			if (e.target === firstPhoneInput) {
				debouncedSearch(e.target.value);
			}
		}
	});
}

/**
 * Инициализация отправки формы
 */
function initFormSubmit() {
	document.addEventListener('submit', async function(e) {
		if (e.target.matches('#contact-modal-form')) {
			e.preventDefault();

			const form = e.target;
			const submitBtn = form.querySelector('#save-contact-btn');
			const spinner = submitBtn?.querySelector('.spinner-border');

			// Показываем загрузку
			if (submitBtn) submitBtn.disabled = true;
			if (spinner) spinner.classList.remove('d-none');

			try {
				const result = await saveContact(form);

				if (result.success) {
					// Добавляем контакт на страницу
					const added = addContactToPage(result.contact);

					if (added !== false) {
						// Закрываем модалку
						const modal = bootstrap.Modal.getInstance(document.getElementById('add-contact-modal'));
						if (modal) modal.hide();

						// Очищаем форму
						clearForm();
					}
				}
			} catch (error) {
				console.error('Error:', error);

				// Показываем ошибки валидации
				if (error.response?.errors) {
					let errorMessage = 'Ошибки:\n';
					Object.values(error.response.errors).forEach(messages => {
						errorMessage += messages.join('\n') + '\n';
					});
					alert(errorMessage);
				} else {
					alert(error.response?.message || 'Произошла ошибка при сохранении');
				}
			} finally {
				if (submitBtn) submitBtn.disabled = false;
				if (spinner) spinner.classList.add('d-none');
			}
		}
	});
}

/**
 * Инициализация удаления контакта
 */
function initRemoveContact() {
	document.addEventListener('click', function(e) {
		const removeBtn = e.target.closest('[data-remove-contact]');
		if (removeBtn) {
			const card = removeBtn.closest('.contact-card');
			const contactId = card?.getAttribute('data-contact-id');

			if (contactId && confirm('Удалить контакт из списка?')) {
				removeContactFromPage(contactId);
			}
		}
	});
}

/**
 * Инициализация редактирования контакта
 */
function initEditContact() {
	document.addEventListener('click', async function(e) {
		const editBtn = e.target.closest('[data-edit-contact]');
		if (editBtn) {
			const card = editBtn.closest('.contact-card');
			const contactId = card?.getAttribute('data-contact-id');

			if (contactId) {
				isEditMode = true;
				currentContactId = contactId;

				// Загружаем данные контакта
				try {
					const response = await fetch(CONFIG.urls.show.replace('{id}', contactId), {
						headers: {
							'Accept': 'application/json',
							'X-Requested-With': 'XMLHttpRequest'
						}
					});

					const data = await response.json();

					if (data.success) {
						// Заполняем форму после открытия модалки
						setTimeout(() => {
							fillFormWithContact(data.contact);
							showFoundIndicator();
						}, 400);
					}
				} catch (error) {
					console.error('Error loading contact:', error);
				}
			}
		}
	});
}

// ========== Инициализация ==========

function init() {
	initModalHandlers();
	initPhoneSearch();
	initFormSubmit();
	initRemoveContact();
	initEditContact();

	// Инициализация daterangepicker если есть
	if ($('#datapiker-contact-modal').length) {
		$('#datapiker-contact-modal').daterangepicker({
			singleDatePicker: true,
			locale: {
				format: 'DD-MM-YYYY',
				separator: ' - ',
				applyLabel: 'Применить',
				cancelLabel: 'Отмена',
				weekLabel: 'Н',
				daysOfWeek: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
				monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
				firstDay: 1
			},
			drops: 'auto'
		});
	}
}

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
    #contact-found-indicator {
        font-size: 12px;
        padding: 4px 8px;
    }
`;
document.head.appendChild(style);

// Запускаем при готовности DOM
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', init);
} else {
	init();
}
