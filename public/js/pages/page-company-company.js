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
			{ data: 'company', name: 'Компания' },
			{ data: 'responsible', name: 'Ответственный' },
			{ data: 'offices', name: 'Офис' },
			{ data: 'command', name: 'Команда' },
			{ data: 'object', name: 'Объ' },
			{ data: 'client', name: 'Кли' },
			{ data: 'succeed', name: 'Усп' },
			{ data: 'nosucceed', name: 'Не усп' },
			{ data: 'commission', name: 'Комиссия' },
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
                           <label class="bookmark">
                              <input type="checkbox">
                              <span>
                                  <img class="non-checked" src="./img/icon/bookmark.svg" alt="">
                                  <img class="on-checked" src="./img/icon/bookmark-cheked.svg" alt="">
                              </span>
                           </label>
                        </div>
                    </div>
                    `;
				}
			}
		],
	});
	table.on('draw', function() {
		// Отримуємо кількість записів, які відображаються
		const recordsDisplay = table.page.info().recordsDisplay;
		
		// Змінюємо текст елемента, обгортаючи кількість записів у тег <b>
		$('#example_info').html('Всего: <b>' + recordsDisplay + '</b>');
	});
	// Обробник кліку на кнопку "btn-others"
	// Обробник кліку на кнопку "btn-others" в блоці client
	$('#example tbody').on('click', '.offices .btn-others', function(e) {
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
            <td colspan="12">
                <div class="table-for-others-info">
                    <p class="paragraph">Офисы</p>
                    <div>
						<div class="thead-wrapper command">
							<p>
								<img src="./img/icon/icon-table/people-fill.svg" alt="">
								<span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Команда">
									<img src="./img/icon/icon-info.svg" alt="">
								</span>
							</p>
						</div>
					</div>
					<div>
						<div class="thead-wrapper object">
							<p>
								<img src="./img/icon/icon-table/house-fill.svg" alt="">
								<span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Объекты">
									<img src="./img/icon/icon-info.svg" alt="">
								</span>
							</p>
						</div>
					</div>
					<div>
						<div class="thead-wrapper client">
							<p>
								<img src="./img/icon/icon-table/person-fill.svg" alt="">
								<span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Сделки">
									<img src="./img/icon/icon-info.svg" alt="">
								</span>
							</p>
						</div>
					</div>
					<div>
						<div class="thead-wrapper succeed">
							<p>
								<img src="./img/icon/icon-table/hand-thumbs-up-fill.svg" alt="">
								<span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Успешные сделки">
									<img src="./img/icon/icon-info.svg" alt="">
								</span>
							</p>
						</div>
					</div>
					<div>
						<div class="thead-wrapper nosucceed">
							<p>
								<img src="./img/icon/icon-table/hand-thumbs-down-fill.svg" alt="">
								<span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Неуспешные сделки">
									<img src="./img/icon/icon-info.svg" alt="">
								</span>
							</p>
						</div>
					</div>
                    <div class="wrapper-btn-collapse">
	                    <button class="info-footer-btn btn-collapse" type="button">Свернуть</button>
					</div>
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
                                <div class="tbody-wrapper photo">
                                    <img src="./img/image.png" alt="">
                                </div>
                            </td>
                             <td>
                                <div class="tbody-wrapper company">
									<strong>Название компании 1</strong>
                                	<p>Генуэзская 1, офис 100</p>
									<span>Аркадия, Одесса, Одесский длинный</span>
								</div>
                            </td>
                            <td>
                                <div class="tbody-wrapper responsible">
                                	<p class="link-name" data-hover-agent>
										Федотов Василий
									</p>
                                	<span>Менеджер</span>
									<a href="tel:380968796542">+380968796542</a>
                                </div>
                            </td>
                            <td>
								<div class="tbody-wrapper command">
									<p><button class="info-footer-btn btn-others" type="button">1000</button></p>
								</div>
							</td>
							<td>
								<div class="tbody-wrapper object">
									<p><button class="info-footer-btn btn-others" type="button">10000</button></p>
								</div>
							</td>
							<td>
								<div class="tbody-wrapper client">
									<p><button class="info-footer-btn btn-others" type="button">1000</button></p>
								</div>
							</td>
							<td>
								<div class="tbody-wrapper succeed">
									<p><button class="info-footer-btn btn-others" type="button">100000</button></p>
								</div>
							</td>
							<td>
								<div class="tbody-wrapper nosucceed">
									<p><button class="info-footer-btn btn-others" type="button">100</button></p>
								</div>
							</td>
                            <td>
                                <div class="tbody-wrapper commission">
                                    <p>от 1000</p>
									<span>до 1000000</span>
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper block-actions">
	                                <a href="#" class="btn mail-link" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="top"
									   data-bs-title="Написать">
										<img src="./img/icon/mail.svg" alt="">
									</a>
                                    <div class="block-actions-wrapper">
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
                                         <label class="bookmark">
                                            <input type="checkbox">
                                            <span>
                                                <img class="non-checked" src="./img/icon/bookmark.svg" alt="">
                                                <img class="on-checked" src="./img/icon/bookmark-cheked.svg" alt="">
                                            </span>
                                        </label>
                                    </div>
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
	$(".js-example-responsive2.currency").select2({
		width: 'resolve',
		minimumResultsForSearch: -1,
	});
	
	$(".js-example-responsive2.company").select2({
		width: 'resolve',
		placeholder: 'Компания',
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
	
	
	new HoverOnInformationAgent({
		containerSelector:'#example',
		hoverAttribute:'data-hover-agent',
		modalClass:'info-agent-modal',
	});
});