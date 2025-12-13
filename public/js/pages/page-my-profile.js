"use strict";

import {
	PhoneInputManager, PhotoLoaderMini,
} from "./function_on_pages-create.js";

$(".js-example-responsive2").select2({
	width: 'resolve',
	minimumResultsForSearch: -1,
});
$("#i-work-with").select2({
	width: 'resolve',
	placeholder: 'гггг',
	minimumResultsForSearch: -1,
});
$("#specialization").select2({
	width: 'resolve',
	placeholder: 'Выберите организацию',
	minimumResultsForSearch: -1,
});
$("#member-organization").select2({
	width: 'resolve',
	placeholder: 'Выберите организацию',
	minimumResultsForSearch: -1,
});
new PhotoLoaderMini({
	inputIdSelector: '#loading-photo',
	wrapperClassSelector: '.photo-info-list',
});
new PhoneInputManager({
	btnSelector: '.btn-new-tel',
	wrapperSelector: '.block-row .phone',
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
	},
	// якщо треба щось дописати то треба дописувати class де зовнішні змінни передавання ззовні(звідси)
});

$('#datapiker').daterangepicker({
	autoUpdateInput: true,
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