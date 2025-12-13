"use strict";

import {
	FileUploader
} from "./function_on_pages-create.js";

// Ініціалізація FileUploader після завантаження Fancybox
function initFileUploaders () {
	// Для документів (без перевірки розміру)
	new FileUploader({
		inputIdSelector: '#document-logo',
		wrapperClassSelector: '.loading-logo',
		renderContainerSelector: '.loading-logo [data-render-document]',
		errorContainer: '.loading-logo .error-container',
		maxCountPhoto: 1,
		checkImageSize: false,
		// якщо треба щось дописати то треба дописувати class де зовнішні змінни передавання ззовні(звідси)
	});
}

// Чекаємо, поки завантажиться Fancybox, якщо ми його тільки що підключили
if (typeof Fancybox !== 'undefined') {
	initFileUploaders();
} else {
	const checkFancybox = setInterval(() => {
		if (window.Fancybox) {
			clearInterval(checkFancybox);
			initFileUploaders();
		}
	}, 200);
}