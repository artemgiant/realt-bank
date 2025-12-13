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
			{ data: 'location', name: 'Локація' },
			{ data: 'type', name: 'Тип' },
			{ data: 'area', name: 'Площа' },
			{ data: 'condition', name: 'Стан' },
			{ data: 'floor', name: 'Поверх' },
			{ data: 'photo', name: 'Фото' },
			{ data: 'price', name: 'Цена от' },
			{ data: 'contact', name: 'Контакт' },
			{
				data: null,
				orderable: false,
				searchable: false,
				render: function (data, type, row) {
					return `
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
                        <button type="button" class="details-control">
                            <img src="./img/icon/plus.svg" alt="">
                        </button>
                    </div>
                    `;
				}
			}
		]
	});
	
	table.on('draw', function() {
		// Отримуємо кількість записів, які відображаються
		const recordsDisplay = table.page.info().recordsDisplay;
		
		// Змінюємо текст елемента, обгортаючи кількість записів у тег <b>
		$('#example_info').html('Всего: <b>' + recordsDisplay + '</b>');
	});
	
	// Обробник кліку на кнопку "деталі" всередині таблиці
	$('#example tbody').on('click', '.details-control', function () {
		const button = $(this);
		const img = button.find('img');
		const row = button.closest('tr');
		const isExpanded = row.next().hasClass('dop-info-row');
		
		if (isExpanded) {
			// Якщо вже розгорнуто - згортаємо
			row.next().remove();
			toggleImage(img);
			row[0].classList.remove('active');
		} else {
			row[0].classList.add('active');
			// Якщо згорнуто - розгортаємо
			toggleImage(img);
			const dopInfoRow = `
                <tr class="dop-info-row">
                    <td colspan="10" style="border-bottom: none;">
                        <div class="tbody-dop-info">
                            <div class="info-main">
                                <div class="info-main-left">
                                    <div class="info-main-left-wrapper">
                                        <div class="description">
                                            <p class="description-text">
                                                <strong>О комплексе:</strong>
                                               Текст примечание для агентов Отличная квартира сдается длительно порядочным людям. Евроремонт свежий. Есть вся мебель и техника и еще описание
												Еще строка текста и если текст длиннее этого то добавляется кнопка развернуть
                                                <span class="more-text" style="display: none;">
                                                    Полное описание квартиры с деталями, которые скрыты по умолчанию.
                                                </span>
                                                <button class="btn btn-show-text" type="button">Ещё</button>
                                            </p>
                                           <div class="description-wrapper">
	                                            <p class="description-text">
	                                                <strong>Примечание для агентов:</strong>
	                                                <span>Текст примечание для агентов Отличная квартира сдается длительно порядочным людям. Евроремонт свежий.</span>
	                                                <span class="more-text" style="display: none;">
                                                    	Полное описание квартиры с деталями, которые скрыты по умолчанию.
                                                	</span>
	                                                <button class="btn btn-show-text" type="button">Ещё</button>
	                                            </p>
	                                            <p class="description-text">
	                                                <strong>Специальные условия:</strong>
	                                                <span>Текст примечание для агентов Отличная квартира сдается длительно порядочным людям. Евроремонт свежий.</span>
	                                                <span class="more-text" style="display: none;">
                                                   		Полное описание квартиры с деталями, которые скрыты по умолчанию.
                                                	</span>
	                                                <button class="btn btn-show-text" type="button">Ещё</button>
	                                            </p>
											</div>
                                        </div>
                                    </div>
                                    <div class="filter-tags">
                                        <div class="badge rounded-pill qwe1">Параметр из фильтра</div>
                                        <div class="badge rounded-pill qwe2">Параметр из фильтра</div>
                                        <div class="badge rounded-pill qwe2">Параметр из фильтра</div>
                                    </div>
                                    <div class="table-for-others">
						                <table id="example2" style="width:98%; margin: auto;">
						                <col width="3.478%" valign="middle">
										<col width="22.174%" valign="middle">
										<col width="6.695%" valign="middle">
										<col width="7.478%" valign="middle">
										<col width="9.13%" valign="middle">
										<col width="5.217%" valign="middle">
										<col width="6.956%" valign="middle">
										<col width="6.782%" valign="middle">
										<col width="14.525%" valign="middle">
										<col width="17.565%" valign="middle">
						                    <tbody>
						                        <tr>
						                        	<td>
						                            	<div class="tbody-wrapper checkBox">
						                            	
														</div>
						                            </td>
						                            <td colspan="2">
						                                <div class="tbody-wrapper location">
						                                    <p>Южная Пальмира дом 1</p>
						                                    <p>Генуэзская 5/1</p>
						                                    <span>Аркадия, Одесса, Одесский</span>
						                                </div>
						                            </td>
						                            <td>
						                            	<div class="tbody-wrapper">
						                            	
														</div>
						                            </td>
						                            <td>
						                                <div class="tbody-wrapper condition">
						                                    <p>Монолит</p>
						                                    <p>Автономное отопление</p>
						                                </div>
						                            </td>
						                            <td>
						                                <div class="tbody-wrapper floor">
						                                    <p>25</p>
						                                </div>
						                            </td>
						                            <td>
						                                <div class="tbody-wrapper photo">
						                                    <img src="./img/image.png" alt="">
						                                </div>
						                            </td>
						                            <td>
						                            	<div class="tbody-wrapper">
						                            	
														</div>
						                            </td>
						                            <td>
						                            	<div class="tbody-wrapper">
						                            	
														</div>
						                            </td>
						                            <td>
							                            <div class="tbody-wrapper">
							                            	2020 г.
														</div>
													</td>
						                        </tr>
						                    </tbody>
						                </table>
						            </div>
                                    <div class="info-footer">
					                    <p class="info-footer-data">ID: <span>1234567</span></p>
					                    <p class="info-footer-data">Добавлено: <span>01.02.2025</span></p>
					                    <p class="info-footer-data">Обновлено: <span>10.02.2025</span>
					                    <!-- ця кнопка є в залежності від ролі/прав користувача -->
					                        <button class="btn" type="button">
						                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#5FB343" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
												  <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9"/>
												  <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z"/>
												</svg>
											</button>
					                    </p>
		                   			 </div>
                                </div>
                            </div>
                            <div class="type-areas">
	                            <ul class="type-areas-list">
	                            	<li class="type-areas-item-info">
	                            		<strong class="type-areas-title">В базе:</strong>
	                            		<p class="type-areas-text">
		                                    <strong>Площадь, м2:</strong>
		                                    <strong>Цена за м2, USD:</strong>
		                                    <strong>Цена за Объект, USD:</strong>
										</p>
									</li>
	                            	<li class="type-areas-item">
	                            		<strong class="type-areas-title">Студии</strong>
	                            		<p class="type-areas-text">
		                                    <span>от 23</span>
		                                    <span>от 10 000</span>
		                                    <span>от 100 000</span>
										</p>
									</li>
									<li class="type-areas-item">
	                                    <strong class="type-areas-title">1 комн</strong>
	                                    <p class="type-areas-text">
		                                    <span>от 30</span>
		                                    <span>от 15 000</span>
		                                    <span>от 150 000</span>
										</p>
									</li>
									<li class="type-areas-item">
	                            		<strong class="type-areas-title">2 комн</strong>
	                            		<p class="type-areas-text">
		                                    <span>от 56</span>
		                                    <span>от 25 000</span>
		                                    <span>от 250 000</span>
										</p>
									</li>
									<li class="type-areas-item">
	                            		<strong class="type-areas-title">3 комн</strong>
	                            		<p class="type-areas-text">
		                                    <span>от 87</span>
		                                    <span>от 35 000</span>
		                                    <span>от 350 000</span>
										</p>
									</li>
									<li class="type-areas-item">
	                            		<strong class="type-areas-title">4 комн</strong>
	                            		<p class="type-areas-text">
		                                   <span>нет в базе</span>
										</p>
									</li>
									<li class="type-areas-item">
	                            		<strong class="type-areas-title">5+ комн</strong>
	                            		<p class="type-areas-text">
		                                    <span>нет в базе</span>
										</p>
									</li>
									<li class="type-areas-item">
	                            		<strong class="type-areas-title">Коммерческая</strong>
	                            		<p class="type-areas-text">
		                                    <span>от 23</span>
		                                    <span>от 18 000</span>
		                                    <span>от 100 000</span>
										</p>
									</li>
	                            	<li class="type-areas-item">
	                            		<strong class="type-areas-title">Дома</strong>
	                            		<p class="type-areas-text">
		                                    <span>от 23</span>
		                                    <span>от 18 000</span>
		                                    <span>от 100 000</span>
										</p>
									</li>
								</ul>
							</div>
							<div class="info-complex-wrapper">
								 <button class="info-complex-btn ms-auto close-btn-other" type="button">Свернуть</button>
							</div>
                        </div>
                    </td>
                </tr>
            `;
			row.after(dopInfoRow);
			// 08.05.2025
			initPhotoHoverPreview();
		}
	});
	
	// Обробник кліку на кнопку "Свернуть"
	$('#example tbody').on('click', '.info-complex-btn', function () {
		const button = $(this);
		// Знаходимо батьківський рядок з деталями
		const dopInfoRow = button.closest('.dop-info-row');
		// Знаходимо попередній рядок (основний)
		const mainRow = dopInfoRow.prev();
		// Знаходимо кнопку details-control в основному рядку
		const detailsControl = mainRow.find('.details-control');
		const img = detailsControl.find('img');
		mainRow[0].classList.remove('active');
		// Змінюємо зображення назад на "+"
		toggleImage(img);
		
		// Видаляємо рядок з деталями
		dopInfoRow.remove();
	});
	
	// Обробник кліку на кнопку "btn-show-text"
	$('#example tbody').on('click', '.btn-show-text', function () {
		const button = $(this);
		const container = button.closest('.description-text');
		const moreText = container.find('.more-text');
		const mainText = container.contents().filter(function () {
			return this.nodeType === 3;
		});
		
		if (moreText.is(':visible')) {
			moreText.hide();
			mainText.show();
			button.text('Ещё');
		} else {
			moreText.show();
			mainText.hide();
			button.text('Скрыть');
		}
	});
	
	function toggleImage(img) {
		const isPlus = img.attr('src').includes('plus.svg');
		img.attr('src', img.attr('src').replace(isPlus ? 'plus.svg' : 'minus.svg', isPlus ? 'minus.svg' : 'plus.svg'));
	}
	
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
		// 08.05.2025
		initPhotoHoverPreview();
	});
	
	// Викликаємо ініціалізацію Tooltip після динамічного додавання рядків
	$('#example tbody').on('click', '.details-control, .details-control-dop, #btn-others', function () {
		setTimeout(() => {
			initTooltips();
		}, 0);
	});
	
	// 08.05.2025
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
				
				// Пропускаємо якщо це дефолтна іконка
				// if (imgSrc.includes('default-foto.svg')) return;
				
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
});