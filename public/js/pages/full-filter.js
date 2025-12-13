$('#datapiker1').daterangepicker({
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
$(document).ready(function () {
	$('#full-filter-btn').on('click', function () {
		// Додаємо або видаляємо клас `active` на кнопці
		$(this).toggleClass('active');
		// Додаємо або видаляємо клас `active` на елементі з класом `full-filter`
		$('.full-filter').toggleClass('active');
	});
});
$(document).ready(function () {
	// Обробник відкриття меню
	$('.multiple-menu-btn').on('click', function (event) {
		event.stopPropagation(); // Зупиняємо всплиття
		const currentState = $(this).attr('data-open-menu');
		const newState = currentState === 'false' ? 'true' : 'false';
		$(this).attr('data-open-menu', newState);
	});
	
	// Обробник кліку поза меню
	$(document).on('click', function () {
		$('.multiple-menu-btn').attr('data-open-menu', 'false');
	});
	
	// Обробник кліку всередині меню, щоб не закривалося при кліку на елементи меню
	$('.multiple-menu-wrapper').on('click', function (event) {
		event.stopPropagation();
	});
	
	// Обробник для всіх чекбоксів
	$(document).on('change', '.multiple-menu-list input[type="checkbox"]', function () {
		const $currentList = $(this).closest('.multiple-menu-list'); // Поточний список
		const $allCheckbox = $currentList.find('input[data-name="checkbox-all"]'); // Чекбокс "Все" в поточному списку
		const $otherCheckboxes = $currentList.find('input[type="checkbox"]').not($allCheckbox); // Інші чекбокси
		if ($(this).data('name') === 'checkbox-all') {
			// Якщо змінений чекбокс "Все"
			if ($(this).is(':checked')) {
				// Якщо "Все" обрано, знімаємо галочки з інших чекбоксів
				$otherCheckboxes.prop('checked', false);
			}
		} else {
			// Якщо змінений будь-який інший чекбокс
			if ($(this).is(':checked')) {
				// Якщо обрано інший чекбокс, знімаємо галочку з "Все"
				$allCheckbox.prop('checked', false);
			}
			// Перевіряємо, чи всі інші чекбокси не вибрані
			if ($otherCheckboxes.filter(':checked').length === 0) {
				// Якщо жоден інший чекбокс не вибрано, ставимо галочку на "Все"
				$allCheckbox.prop('checked', true);
			}
		}
	});
});