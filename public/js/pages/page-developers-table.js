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
			{ data: 'developer', name: 'Девелопер' },
			{ data: 'year', name: 'Год' },
			{ data: 'complex', name: 'Комплексы' },
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
		toggleImage(img);
		const row = button.closest('tr');
		const isExpanded = row.next().hasClass('dop-info-row');
		if (isExpanded) {
			row[0].classList.remove('active');
			row.next().remove();
		} else {
			row[0].classList.add('active');
			const dopInfoRow = `
                <tr class="dop-info-row">
                    <td colspan="10" style="border-bottom: none;">
                        <div class="tbody-dop-info">
                            <div class="info-main">
                                <div class="info-main-left">
                                    <div class="info-main-left-wrapper">
                                        <div class="description">
                                            <p class="description-text">
                                                <strong>О девелопере:</strong>
                                               Текст примечание для агентов Отличная квартира сдается длительно порядочным людям. Евроремонт свежий. Есть вся мебель и техника и еще описание
Еще строка текста и если текст длиннее этого то добавляется кнопка развернуть
                                                <span class="more-text" style="display: none;">
                                                    Полное описание квартиры с деталями, которые скрыты по умолчанию.
                                                </span>
                                                <button class="btn btn-show-text" type="button">Ещё</button>
                                            </p>
                                            <p class="description-note">
                                                <strong>Примечание для агентов:</strong>
                                                <span>Текст примечание для агентов Отличная квартира сдается длительно порядочным людям. Евроремонт свежий. Есть вся мебель и техника и еще описание</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
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
                                <p class="info-footer-data">Комплексы: <button class="info-footer-btn btn-others" type="button">3</button></p>
                            	<button class="info-footer-btn ms-auto close-btn-other" type="button">Свернуть</button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
			row.after(dopInfoRow);
		}
	});
	
	// Обробник кліку на кнопку "btn-others"
	$('#example tbody').on('click', '.btn-others', function () {
		const button = $(this);
		const dopInfoRow = button.closest('.dop-info-row');
		const tbodyDopInfo = dopInfoRow.find('.tbody-dop-info');
		const isOthersTableAdded = tbodyDopInfo.next().hasClass('table-for-others');
		
		// Знаходимо кнопку "info-footer-btn", яка знаходиться поряд з #btn-others
		const infoFooterBtn = button.closest('.info-footer').find('.close-btn-other');
		
		if (isOthersTableAdded) {
			tbodyDopInfo.next().remove();
			infoFooterBtn.removeClass('active'); // Видаляємо клас "active", якщо таблиця видаляється
		} else {
			const othersTable = `
            <div class="table-for-others">
                <table id="example2" style="width:98%; margin: 0 auto;">
                    <col width="25.652%" valign="middle">
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
                                <div class="tbody-wrapper location">
                                    <p>Южная Пальмира дом 1</p>
	                                <p>Генуэзская 5/1</p>
	                                <span>Аркадия, Одесса, Одесский</span>
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper type">
                               		<p>студии,</p>
									<span>1 к, 2 к, 3 к, 4 к, 5+, ком</span>
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper area">
                                    <p>25 - 90 м²</p>
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
                                    <p>25 эт.</p>
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper photo">
                                    <img src="./img/image.png" alt="">
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper price">
                                    <p>от 85000</p>
                                    <span>от 850/м²</span>
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper contact">
                                    <div>
	                                    <p class="link-name" data-hover-contact>
											Федотов Василий
										</p>
	                                    <p data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Компания">Компания</p>
	                                    <a href="tel:380968796542">+380968796542</a>
									</div>
                                    <div>
										<img src="./img/complex3.png" alt="">
									</div>
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
        `;
			tbodyDopInfo.after(othersTable);
			infoFooterBtn.addClass('active'); // Додаємо клас "active", якщо таблиця додається
			// 08.05.2025
			initPhotoHoverPreview();
			// Ініціалізуємо тултіпи для новостворених елементів
			initTooltips();
		}
	});
	
	// Обробник кліку на кнопку "close-btn-other"
	$('#example tbody').on('click', '.close-btn-other', function () {
		const button = $(this);
		const dopInfoRow = button.closest('.dop-info-row');
		const tbodyDopInfo = dopInfoRow.find('.tbody-dop-info');
		const othersTable = tbodyDopInfo.next('.table-for-others');
		
		if (othersTable.length) {
			othersTable.remove();
			button.removeClass('active');
		}
	});
	
	// Обробник кліку на кнопку "details-control-dop"
	$('#example tbody').on('click', '.details-control-dop', function () {
		const button = $(this);
		const img = button.find('img');
		toggleImage(img);
		const row = button.closest('tr');
		const isExpanded = row.next().hasClass('dop-info-row-dop');
		if (isExpanded) {
			row.next().remove();
		} else {
			const dopInfoRow = `
		 		 <tr class="dop-info-row-dop">
		           <td colspan="9">
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
                                </div>
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
                    </td>
                </tr>
        `;
			row.after(dopInfoRow);
		}
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
	// Обробник кліку на кнопку "btn-show-text"
	$('#example tbody').on('click', '.btn-show-text2', function () {
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
		initPhotoHoverPreview();
	});
	// Додаємо цей код до вашого існуючого $(document).ready()
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
	// Викликаємо ініціалізацію Tooltip після динамічного додавання рядків
	$('#example tbody').on('click', '.details-control, .details-control-dop, #btn-others', function () {
		setTimeout(() => {
			initTooltips();
		}, 0);
	});
	
});