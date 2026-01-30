"use strict";

import {
	PhotoLoaderMini,
	PhoneInputManager,
} from "./function_on_pages-create.js";

$("#agency-branch-metro").select2({
	width: 'resolve',
	placeholder: '-',
	minimumResultsForSearch: -1,
});

$(".js-example-responsive2").select2({
	width: 'resolve',
	minimumResultsForSearch: -1,
});



new PhotoLoaderMini({
	inputIdSelector: '#loading-photo',
	wrapperClassSelector: '.photo-info-list',
});

// Инициализация PhoneInputManager для модального окна контакта
new PhoneInputManager({
	btnSelector: '.btn-new-tel',
	wrapperSelector: '#add-contact-modal .modal-row .item.phone',
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
