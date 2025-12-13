import {HoverOnInformationAgent} from "./info-agent-or-contact-modal.js";

$(document).ready(function () {
	const table = $('#example').DataTable({
		searching: false,
		ordering: false,
		processing: false,
		pagingType: "simple_numbers",
		language: {
			url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/ru.json" // Підключення локалізації
		},
		columns: [
			{
				data: null,
				orderable: false,
				searchable: false,
				render: function (data, type, row) {
					return `
                      <div class="tbody-wrapper checkBox">
                          <label class="my-custom-input">
                              <input type="checkbox">
                              <span class="my-custom-box"></span>
                          </label>
                      </div>
                    `;
				}
			},
			{ data: 'photo', name: 'Фото' },
			{ data: 'agent', name: 'Агент' },
			{ data: 'position', name: 'Должность' },
			{ data: 'offices', name: 'Офис' },
			{ data: 'object', name: 'Объ' },
			{ data: 'client', name: 'Кли' },
			{ data: 'succeed', name: 'Усп' },
			{ data: 'nosucceed', name: 'Не усп' },
			{ data: 'activeuntil', name: 'Активный до' },
			{
				data: null,
				orderable: false,
				searchable: false,
				render: function (data, type, row) {
					return `
                     <div class="tbody-wrapper block-actions">
                     	<a href="#" class="btn mail-link" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="top"
						   data-bs-title="Написать">
							<img src="./img/icon/mail.svg" alt="">
						</a>
                        <div class="block-actions-wrapper">
                           <label class="bookmark">
                              <input type="checkbox">
                              <span>
                                  <img class="non-checked" src="./img/icon/bookmark.svg" alt="">
                                  <img class="on-checked" src="./img/icon/bookmark-cheked.svg" alt="">
                              </span>
                           </label>
                           <div class="menu-burger">
                              <div class="dropdown">
                                 <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                     <img src="./img/icon/burger-blue.svg" alt="">
                                 </button>
                                 <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Обновить</a></li>
                                    <li><a class="dropdown-item" href="#">Редактировать</a></li>
                                    <li><a class="dropdown-item" href="#">Удалить</a></li>
                                    <li><a class="dropdown-item" href="#">Отложить</a></li>
                                    <li><a class="dropdown-item" href="#">Передати</a></li>
                                 </ul>
                              </div>
                           </div>
                           <div class="menu-info">
                              <div class="dropdown">
                                 <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                     <img src="./img/icon/copylinked.svg" alt="">
                                 </button>
                                 <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><span>На сайте</span></a></li>
                                    <li><a class="dropdown-item" href="#"><span>На Rem.ua</span></a></li>
                                    <li><a class="dropdown-item" href="#"><span>Видео Youtube</span></a></li>
                                    <li><a class="dropdown-item" href="#"><span>На карте</span></a></li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                    </div>
                    `;
				}
			}
		],
		
		// Добавьте этот callback для инициализации после создания таблицы
		initComplete: function(settings, json) {
			// Инициализация Select2 для элементов, которые уже есть в DOM
			$(".js-example-responsive3.position-select").select2({
				width: 'resolve',
				placeholder: 'Должность',
			});
			// Инициализация Select2 для элементов, которые уже есть в DOM
			$(".js-example-responsive3.offices-select").select2({
				width: 'resolve',
				placeholder: 'Офис',
			});
		},
		// Или используйте drawCallback для инициализации при каждом перерисовывании таблицы
		drawCallback: function(settings) {
			$(".js-example-responsive3.position-select").select2({
				width: 'resolve',
				placeholder: 'Должность',
			});
			// Инициализация Select2 для элементов, которые уже есть в DOM
			$(".js-example-responsive3.offices-select").select2({
				width: 'resolve',
				placeholder: 'Офис',
			});
		}
	});
	table.on('draw', function() {
		// Отримуємо кількість записів, які відображаються
		const recordsDisplay = table.page.info().recordsDisplay;
		
		// Змінюємо текст елемента, обгортаючи кількість записів у тег <b>
		$('#example_info').html('Всего: <b>' + recordsDisplay + '</b>');
	});
	// Обробник кліку на кнопку "btn-others"
	// Обробник кліку на кнопку "btn-others" в блоці client
	$('#example tbody').on('click', '.object .btn-others', function(e) {
		e.stopPropagation(); // Зупиняємо спливання події
		
		const button = $(this);
		const row = button.closest('tr');
		const isExpanded = row.next().hasClass('dop-info-row');
		
		// Якщо рядок вже розгорнутий - нічого не робимо
		if (isExpanded) {
			row[0].classList.remove('active');
			row.next().remove();
		} else {
			row[0].classList.add('active');
			// Створюємо новий рядок з таблицею
			const dopInfoRow = `
        <tr class="dop-info-row">
            <td colspan="11">
                <div class="table-for-others-info">
                    <p class="paragraph">Объекты</p>
                    <button class="info-footer-btn btn-collapse" type="button">Свернуть</button>
				</div>
                <div class="table-for-others">
                    <table id="example2" style="width:98%;  margin: auto;">
                        <col width="27.46763%" valign="middle">
                        <col width="8.51063%" valign="middle">
                        <col width="9.29363%" valign="middle">
                        <col width="10.94563%" valign="middle">
                        <col width="7.03262%" valign="middle">
                        <col width="8.77162%" valign="middle">
                        <col width="8.59762%" valign="middle">
                        <col width="19.38062%" valign="middle">
                        <tbody>
                        <tr>
                            <td>
                                <div class="tbody-wrapper location">
                                    <p>Южная Пальмира</p>
                                    <p>Генуэзская/Посмитного</p>
                                    <span>Аркадия, Одесса, Одесская</span>
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper type">
                                    <p>2к</p>
                                    <span>Квартира</span>
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper area">
                                    <p>60/40/15</p>
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper condition">
                                    <p>С ремонтом</p>
                                    <p>Новострой</p>
                                    <span> Монолит</span>
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper floor">
                                    <p>4/25 <span>эт</span></p>
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper photo">
                                    <img src="./img/image.png" alt="">
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper price">
                                    <p>85000</p>
                                    <span>850/м <sup>2</sup></span>
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper block-actions">
                                    <div class="block-actions-wrapper">
                                        <label class="bookmark">
                                            <input type="checkbox">
                                            <span>
                                                <img class="non-checked" src="./img/icon/bookmark.svg" alt="">
                                                <img class="on-checked" src="./img/icon/bookmark-cheked.svg" alt="">
                                            </span>
                                        </label>
                                        <div class="menu-burger">
                                            <div class="dropdown">
                                                <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <img src="./img/icon/burger-blue.svg" alt="">
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">Обновить</a></li>
                                                    <li><a class="dropdown-item" href="#">Редактировать</a></li>
                                                    <li><a class="dropdown-item" href="#">Удалить</a></li>
                                                    <li><a class="dropdown-item" href="#">Отложить</a></li>
                                                    <li><a class="dropdown-item" href="#">Передать</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="menu-info">
                                            <div class="dropdown">
                                                <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <img src="./img/icon/copylinked.svg" alt="">
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><span>На сайте</span></a></li>
                                                    <li><a class="dropdown-item" href="#"><span>На Rem.ua</span></a></li>
                                                    <li><a class="dropdown-item" href="#"><span>Видео Youtube</span></a></li>
                                                    <li><a class="dropdown-item" href="#"><span>На карте</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="details-control-dop">
                                        <img src="./img/icon/plus.svg" alt="">
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    `;
			// Додаємо новий рядок після поточного
			row.after(dopInfoRow);
		};
		
		// Ініціалізуємо tooltips для нових елементів
		initTooltips();
		initPhotoHoverPreview();
	});
	// Обробник кліку для кнопки "Свернуть"
	$('#example tbody').on('click', '.btn-collapse', function(e) {
		e.stopPropagation();
		
		const closeButton = $(this);
		// Знаходимо батьківський рядок dop-info-row
		const dopInfoRow = closeButton.closest('.dop-info-row');
		
		// Знаходимо попередній рядок (основний) - використовуємо jQuery об'єкт
		const mainRow = dopInfoRow.prev();
		
		// Видаляємо клас active з основного рядка
		mainRow.removeClass('active');
		
		// Видаляємо рядок з деталями
		dopInfoRow.remove();
	});
	// робота інтупів
	$('thead .my-custom-input input').on('change', function() {
		let isChecked = $(this).prop('checked');
		$('tbody .my-custom-input input').prop('checked', isChecked);
	}).end().on('change', 'tbody .my-custom-input input', function() {
		let allChecked = $('tbody .my-custom-input input:checked').length ===
			$('tbody .my-custom-input input').length;
		$('thead .my-custom-input input').prop('checked', allChecked);
	});
	
	const initTooltips = function () {
		const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
		const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
	};
	
	// Викликаємо ініціалізацію Tooltip після створення таблиці
	initTooltips();
	
	// Викликаємо ініціалізацію Tooltip після оновлення таблиці
	table.on('draw', function () {
		initTooltips();
	});
	
	// Викликаємо ініціалізацію Tooltip після динамічного додавання рядків
	$('#example tbody').on('click', '.details-control, .details-control-dop, #btn-others', function () {
		setTimeout(() => {
			initTooltips();
			
		}, 0);
	});
	$(".js-example-responsive2.position").select2({
		width: 'resolve',
		placeholder: 'Должность',
	});
	$(".js-example-responsive2.statusagents").select2({
		width: 'resolve',
		placeholder: 'Статус агентов',
	});
	$(".js-example-responsive2.company").select2({
		width: 'resolve',
		placeholder: 'Компания',
	});
	$(".js-example-responsive2.offices").select2({
		width: 'resolve',
		placeholder: 'Офис',
	});
	
	function initPhotoHoverPreview() {
		// Створюємо попап для прев'ю фото (якщо ще не існує)
		if ($('#photo-preview-popup').length === 0) {
			$('body').append(`
            <div id="photo-preview-popup">
                <img src="" alt="">
            </div>
        `);
		}
		
		const $popup = $('#photo-preview-popup');
		const $popupImg = $popup.find('img');
		const $closeBtn = $('#close-photo-preview');
		let hoverTimeout;
		
		// Обробник наведення на фото
		$('.tbody-wrapper.photo img').hover(
			function() {
				const $img = $(this);
				const imgSrc = $img.attr('src');
				
				hoverTimeout = setTimeout(function() {
					$popupImg.attr('src', imgSrc);
					$popup.show();
				}, 300);
			},
			function() {
				clearTimeout(hoverTimeout);
				$popup.hide();
			}
		);
		
		// Обробник наведення на сам попап
		$popup.hover(
			function() {
				// Не ховаємо попап при наведенні на нього
			},
			function() {
				$popup.hide();
			}
		);
		
		// Обробник кліку на кнопку закриття
		$closeBtn.on('click', function() {
			$popup.hide();
		});
	}

	// Ініціалізуємо функціонал при завантаженні сторінки
	initPhotoHoverPreview();
	// Викликаємо ініціалізацію Tooltip після оновлення таблиці
	table.on('draw', function () {
		initPhotoHoverPreview();
	});
	
	$('#datapiker').daterangepicker({
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
	new HoverOnInformationAgent({
		containerSelector:'#example',
		hoverAttribute:'data-hover-agent',
		modalClass:'info-agent-modal',
	}); // Для агентів
	
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