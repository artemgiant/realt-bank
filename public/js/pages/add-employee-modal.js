import {PhotoLoaderMini, PhoneInputManager } from "./function_on_pages-create.js";
(function() {


let phoneManager = null;
let select2Initialized = false;

function initPhoneInputManager() {
	if (phoneManager) return;
	
	if (document.querySelector('.btn-new-tel') && typeof PhoneInputManager !== 'undefined') {
		try {
			phoneManager = new PhoneInputManager({
				btnSelector: '.btn-new-tel',
				wrapperSelector: '#add-employee-modal .modal-row .item.phone',
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

function initSelect2() {
	if (select2Initialized) return;
	
	// Масив всіх Select2, які потрібно ініціалізувати
	const select2Configs = [
		{
			selector: '#tags-employee-modal',
			options: {
				dropdownParent: $('#add-employee-modal'),
				width: '100%',
				placeholder: 'Выберите теги',
				language: { noResults: () => "Результатов не найдено" }
			}
		},
		{
			selector: '#role-employee-modal',
			options: {
				dropdownParent: $('#add-employee-modal'),
				width: '100%',
				placeholder: 'Выберите роль',
				language: { noResults: () => "Результатов не найдено" }
			}
		},
		{
			selector: '#offices-employee-modal',
			options: {
				dropdownParent: $('#add-employee-modal'),
				width: '100%',
				placeholder: 'Выберите офис',
				language: { noResults: () => "Результатов не найдено" }
			}
		}
	];
	
	try {
		// Ініціалізуємо всі Select2
		select2Configs.forEach(config => {
			if ($(config.selector).length) {
				// Спочатку destroy, якщо вже ініціалізовано
				if ($(config.selector).data('select2')) {
					$(config.selector).select2('destroy');
				}
				
				$(config.selector).select2(config.options);
				
				// Додаємо обробник для фокусу
				$(config.selector).on('select2:open', function() {
					setTimeout(() => {
						const searchField = document.querySelector('.select2-search__field');
						if (searchField) {
							searchField.focus();
						}
					}, 100);
				});
			}
		});
		
		select2Initialized = true;
		console.log('All Select2 initialized successfully');
		
	} catch (error) {
		console.error('Error initializing Select2:', error);
	}
}


// Ініціалізація FileUploader для документів
function initPhotoLoader () {
	const modalElement = document.getElementById('add-employee-modal');
	if (modalElement) {
		new PhotoLoaderMini({
			inputIdSelector: '#loading-photo-employee-modal',
			wrapperClassSelector: '.photo-info-list',
			context: modalElement // ← передаємо контекст модалки
		});
	}
}
function initModalComponents() {
	initPhoneInputManager();
	initSelect2();
	initPhotoLoader();
}

// Обробник для модалки
const modal = document.getElementById('add-employee-modal');
if (modal) {
	modal.addEventListener('shown.bs.modal', function() {
		setTimeout(initModalComponents, 300);
	});
	
	modal.addEventListener('hidden.bs.modal', function() {
		// Очищаємо PhoneInputManager
		if (phoneManager && typeof phoneManager.destroy === 'function') {
			phoneManager.destroy();
			phoneManager = null;
		}
		
		// Очищаємо всі Select2
		const select2Selectors = ['#tags-employee-modal', '#role-employee-modal', '#offices-employee-modal'];
		select2Selectors.forEach(selector => {
			if ($(selector).data('select2')) {
				$(selector).select2('destroy');
			}
		});
		
		select2Initialized = false;
		console.log('All components destroyed');
	});
}

$('#datapiker-employee-modal').daterangepicker({
	singleDatePicker: true,
	"locale": {
		"format": "DD-MM-YYYY",
		"separator": " - ",
		"applyLabel": "Применить",
		"cancelLabel": "Отмена",
		"fromLabel": "From",
		"toLabel": "To",
		"customRangeLabel": "Custom",
		"weekLabel": "Н",
		"daysOfWeek": [
			"Вс",
			"Пн",
			"Вт",
			"Ср",
			"Чт",
			"Пт",
			"Сб"
		],
		"monthNames": [
			"Январь",
			"Февраль",
			"Март",
			"Апрель",
			"Май",
			"Июнь",
			"Июль",
			"Август",
			"Сентябрь",
			"Октябрь",
			"Ноябрь",
			"Декабрь"
		],
		"firstDay": 1
	},
	"drops": "auto"
});



// Додаємо CSS виправлення для Select2
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
`;

document.head.appendChild(style);

})();