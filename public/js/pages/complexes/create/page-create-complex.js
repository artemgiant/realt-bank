"use strict";

import {
	FileUploader,
	PhotoLoader
} from "./function_on_pages-create.js";

// ================= ИНИЦИАЛИЗАЦИЯ SELECT2 =================
$(".js-example-responsive2-currency").select2({
	width: 'resolve',
	placeholder: 'Валюта',
	minimumResultsForSearch: -1,
});

$(".js-example-responsive2").select2({
	width: 'resolve',
	placeholder: 'Выбрать',
	minimumResultsForSearch: -1,
});
$(".js-example-responsive3").select2({
	width: 'resolve',
	placeholder: 'Выбрать',
});
$(".js-example-responsive4").select2({
	width: 'resolve',
	placeholder: 'Введите теги через запятую',
});
$(".js-example-responsive5").select2({
	width: 'resolve',
	placeholder: '--',
});

// ================= ДЕВЕЛОПЕР SELECT2 С AJAX =================
$('#developer_id').select2({
	width: 'resolve',
	placeholder: 'Выберите девелопера',
	allowClear: true,
	ajax: {
		url: '/developers/ajax-search',
		dataType: 'json',
		delay: 250,
		data: function (params) {
			return {
				q: params.term
			};
		},
		processResults: function (data) {
			return {
				results: data.results
			};
		},
		cache: true
	},
	minimumInputLength: 0
});

// ================= УПРАВЛЕНИЕ СЕКЦИЯМИ/КОРПУСАМИ =================
const BlocksManager = {
	blockIndex: 1, // Начинаем с 1, так как 0 уже есть в HTML

	init() {
		this.bindEvents();
		this.updateRemoveButtons();
	},

	bindEvents() {
		// Добавление новой секции
		$('#add-block-btn').on('click', () => this.addBlock());

		// Удаление секции (делегирование событий)
		$('#blocks-list').on('click', '.btn-remove-block', (e) => {
			const blockItem = $(e.currentTarget).closest('.block-item');
			this.removeBlock(blockItem);
		});
	},

	addBlock() {
		const template = document.getElementById('block-template');
		if (!template) {
			console.error('Block template not found');
			return;
		}

		// Клонируем содержимое шаблона
		const clone = template.content.cloneNode(true);
		const blockHtml = clone.querySelector('.block-item').outerHTML;

		// Заменяем __INDEX__ на реальный индекс
		const newBlockHtml = blockHtml.replace(/__INDEX__/g, this.blockIndex);

		// Добавляем в список
		$('#blocks-list').append(newBlockHtml);

		// Инициализируем Select2 для новых селектов
		const newBlock = $(`#blocks-list .block-item[data-block-index="${this.blockIndex}"]`);
		newBlock.find('.js-example-responsive3').select2({
			width: 'resolve',
			placeholder: 'Выбрать',
		});

		// Инициализируем Select2 для улиц с AJAX
		this.initStreetSelect(newBlock.find('.block-street-select'));

		// Инициализируем загрузчик планов для нового блока
		if (typeof window.initBlockPlanUploader === 'function') {
			window.initBlockPlanUploader(this.blockIndex);
		}

		// Увеличиваем индекс
		this.blockIndex++;

		// Обновляем видимость кнопок удаления
		this.updateRemoveButtons();
	},

	removeBlock(blockItem) {
		const blocksCount = $('#blocks-list .block-item').length;

		// Не даем удалить последний блок
		if (blocksCount <= 1) {
			alert('Должна остаться хотя бы одна секция');
			return;
		}

		// Удаляем Select2 перед удалением элемента
		blockItem.find('select').each(function() {
			if ($(this).hasClass('select2-hidden-accessible')) {
				$(this).select2('destroy');
			}
		});

		// Удаляем блок
		blockItem.remove();

		// Обновляем видимость кнопок удаления
		this.updateRemoveButtons();
	},

	updateRemoveButtons() {
		const blocksCount = $('#blocks-list .block-item').length;

		// Показываем/скрываем кнопки удаления
		$('#blocks-list .btn-remove-block').each(function() {
			if (blocksCount > 1) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
	},

	initStreetSelect(selectElement) {
		$(selectElement).select2({
			width: 'resolve',
			placeholder: 'Выберите улицу',
			allowClear: true,
			ajax: {
				url: '/location/search',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					// Получаем state_id из формы локации
					const stateId = $('#state_id').val() || $('input[name="state_id"]').val();
					return {
						q: params.term,
						state_id: stateId
					};
				},
				processResults: function (data) {
					// API возвращает { success: true, results: [...] }
					const results = data.results || [];
					return {
						results: results.map(function(item) {
							return {
								id: item.id,
								text: item.name + (item.city_name ? ' (' + item.city_name + ')' : '')
							};
						})
					};
				},
				cache: true
			},
			minimumInputLength: 2
		});
	}
};

// Инициализируем Select2 для улиц первого блока
BlocksManager.initStreetSelect($('#blocks-0-street_id'));

// Инициализируем менеджер блоков
BlocksManager.init();

$('.my-select2').on('select2:opening', function (e) {
	$('.filter select').attr("style", "display: none !important");
});

$('.my-select2').on('select2:closing', function (e) {
	$('.filter select').attr("style", "display: block !important");
});

// Ініціалізація FileUploader після завантаження Fancybox
function initFileUploaders () {
	// Для логотипа
	new FileUploader({
		inputIdSelector: '#logo',
		wrapperClassSelector: '.loading-logo',
		renderContainerSelector: '.loading-logo [data-render-document]',
		errorContainer: '.loading-logo .error-container',
		maxCountPhoto: 1,
		checkImageSize: false,
	});

	// Для планов комплекса
	new FileUploader({
		inputIdSelector: '#plans',
		wrapperClassSelector: '.loading-plan',
		renderContainerSelector: '.loading-plan [data-render-document]',
		errorContainer: '.loading-plan .error-container',
		maxCountPhoto: 10,
		checkImageSize: false,
	});

	// Для фото комплекса
	new PhotoLoader({
		inputId: 'photos',
		checkImageSize: false,
		minWidth: 800,
		minHeight: 800,
		wrapperClass: 'photo-info-list',
		maxPhotos: 20,
	});

	// Инициализируем загрузчик планов для первого блока (index 0)
	initBlockPlanUploader(0);
}

// Функция для инициализации загрузчика плана секции
function initBlockPlanUploader(blockIndex) {
	const inputSelector = `#blocks-${blockIndex}-plan`;
	const wrapperSelector = `[data-plan-id="plan-${blockIndex}"]`;
	const renderSelector = `[data-plan-id="plan-file-${blockIndex}"] [data-render-document]`;
	const errorSelector = `[data-plan-id="plan-file-${blockIndex}"] [data-error]`;

	// Проверяем существование элементов
	if ($(inputSelector).length && $(wrapperSelector).length) {
		new FileUploader({
			inputIdSelector: inputSelector,
			wrapperClassSelector: wrapperSelector,
			renderContainerSelector: renderSelector,
			errorContainer: errorSelector,
			maxCountPhoto: 1,
			checkImageSize: false,
		});
	}
}

// Экспортируем функцию для использования при добавлении новых блоков
window.initBlockPlanUploader = initBlockPlanUploader;

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

$(document).ready(function () {
	// Обробник відкриття меню для всіх multiple-menu
	// (conditions, housing-classes, categories, object-types)
	// features-menu обробляється в features-tags.js
	$('#conditions-menu .multiple-menu-btn, #housing-classes-menu .multiple-menu-btn, #categories-menu .multiple-menu-btn, #object-types-menu .multiple-menu-btn').on('click', function (event) {
		event.stopPropagation();
		const $this = $(this);
		const currentState = $this.attr('data-open-menu');
		const newState = currentState === 'false' ? 'true' : 'false';

		// Закриваємо всі інші відкриті меню
		$('.multiple-menu-btn').not($this).attr('data-open-menu', 'false');
		// Відкриваємо/закриваємо поточне меню
		$this.attr('data-open-menu', newState);
	});

	// Обробник кліку поза меню
	$(document).on('click', function (e) {
		// Не закриваємо, якщо клік був всередині меню
		if (!$(e.target).closest('.multiple-menu').length) {
			$('.multiple-menu-btn').attr('data-open-menu', 'false');
		}
	});

	// Обробник кліку всередині меню, щоб не закривалося при кліку на елементи меню
	$('.multiple-menu-wrapper').on('click', function (event) {
		event.stopPropagation();
	});
});