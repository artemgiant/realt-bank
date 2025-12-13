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
					// нове
					return `
                      <div class="tbody-wrapper checkBox">
                          <label class="my-custom-input">
                              <input type="checkbox">
                              <span class="my-custom-box"></span>
                          </label>
                          <span class="last-update-item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip-red" data-bs-title="Обновлено более 30 дней назад"></span>
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
			{ data: 'price', name: 'Ціна' },
			{ data: 'contact', name: 'Контакт' },
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
                                            <h2 class="description-title">Заголовок текст 1 к кв пл Толбухина срочно</h2>
                                            <p class="description-text">
                                                Отличная квартира сдается длительно порядочным людям. Евроремонт свежий. Есть
                                                вся мебель и техника и еще описание
                                                <span class="more-text" style="display: none;">
                                                    Полное описание квартиры с деталями, которые скрыты по умолчанию.
                                                </span>
                                                <button class="btn btn-show-text" type="button">Ещё</button>
                                            </p>
                                            <p class="description-note">
                                                <strong>Примечание для агентов:</strong>
                                                <span>Текст примечание для агентов Отличная квартира сдается длительно порядочным людям. Евроремонт свежий. Есть вся мебель и техника и еще описание</span>
                                            </p>
                                            <p class="description-note">
                                                <strong>Заметка:</strong>
                                                <span>Покупает тому-то, продает свою там-то, звонить тогда-то текст личной заметки</span>
                                            </p>
                                        </div>
                                        <!-- якщо немає контакту у об'єкта в БД тоді нічого з нижніх блоків не дображаеться (ще краще за логіку запитати у Вадіма)
                                        також може бути потрібне це реалізілувати в нижньму блоці яки розгортаеться з доп внутрішньої таблички
                                        -->
                                         <!-- цей блок що нижче буде показуватись якщо нестоїть галочка понатисканню повина з'явитись сторінка або модалка для встановлення дозволу
                                           на відображення контакту\кнопки показати контакт -->
                                        <ul class="block-info">
                                        	 <li class="block-info-item">
	                                            <div class="info-btn-wrapper">
		                                            <a class="btn btn-outline-primary" href="#">
		                                         	   Связаться с агентом
													</a>
												</div>
                                            </li>
                                        </ul>
                                        <!-- цей блок що нижче буде показуватись якщо стоїться галочка на довід відображення контакта, і якщо на неї натиснули
                                         то буде відображатись наступний блок що нижче і цей блок з кнопкой зникне -->
<!--                                        <ul class="block-info">-->
<!--                                        	 <li class="block-info-item">-->
<!--		                                        <div class="info-title-wrapper">-->
<!--		                                       		<h2 class="info-title">Клиент</h2>-->
<!--	                                            </div>-->
<!--	                                            <div class="info-btn-wrapper">-->
<!--		                                            <button class="btn btn-outline-primary" type="button">-->
<!--		                                         	   Показать контакты-->
<!--													</button>-->
<!--												</div>-->
<!--                                            </li>-->
<!--                                        </ul>-->
                                       <!--цей блок може з'явитись і замінити інший якщо користувач натиснув на кнопку  -->
										<!--	<ul class="block-info"> -->
<!--											 <li class="block-info-item">-->
<!--		                                        <div class="info-title-wrapper">-->
<!--		                                       		<h2 class="info-title">Клиент</h2>-->
<!--		                                       		<button class="btn  btn-edit-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">-->
<!--			                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--														  <path d="M2.33398 10.9996H5.16065C5.24839 11.0001 5.33536 10.9833 5.41659 10.9501C5.49781 10.917 5.57169 10.8681 5.63398 10.8063L10.2473 6.1863L12.1406 4.33297C12.2031 4.27099 12.2527 4.19726 12.2866 4.11602C12.3204 4.03478 12.3378 3.94764 12.3378 3.85963C12.3378 3.77163 12.3204 3.68449 12.2866 3.60325C12.2527 3.52201 12.2031 3.44828 12.1406 3.3863L9.31398 0.5263C9.25201 0.463815 9.17828 0.414219 9.09704 0.380373C9.0158 0.346527 8.92866 0.329102 8.84065 0.329102C8.75264 0.329102 8.66551 0.346527 8.58427 0.380373C8.50303 0.414219 8.42929 0.463815 8.36732 0.5263L6.48732 2.41297L1.86065 7.03297C1.79886 7.09526 1.74998 7.16914 1.7168 7.25036C1.68363 7.33159 1.66681 7.41856 1.66732 7.5063V10.333C1.66732 10.5098 1.73756 10.6793 1.86258 10.8044C1.9876 10.9294 2.15717 10.9996 2.33398 10.9996ZM8.84065 1.93963L10.7273 3.8263L9.78065 4.77297L7.89398 2.8863L8.84065 1.93963ZM3.00065 7.77963L6.95398 3.8263L8.84065 5.71297L4.88732 9.6663H3.00065V7.77963ZM13.0007 12.333H1.00065C0.82384 12.333 0.654271 12.4032 0.529246 12.5282C0.404222 12.6533 0.333984 12.8228 0.333984 12.9996C0.333984 13.1764 0.404222 13.346 0.529246 13.471C0.654271 13.5961 0.82384 13.6663 1.00065 13.6663H13.0007C13.1775 13.6663 13.347 13.5961 13.4721 13.471C13.5971 13.346 13.6673 13.1764 13.6673 12.9996C13.6673 12.8228 13.5971 12.6533 13.4721 12.5282C13.347 12.4032 13.1775 12.333 13.0007 12.333Z" fill="#AAAAAA" />-->
<!--														</svg>-->
<!--													</button>-->
<!--		                                       		<button class="btn  btn-add-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">-->
<!--			                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--														  <path d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z" fill="#AAAAAA" />-->
<!--														  <path d="M8.583 13.5848C8.46922 13.471 8.39889 13.3139 8.39889 13.1403L8.39889 4.91816C8.39883 4.571 8.68022 4.28961 9.02739 4.28967C9.37449 4.28967 9.65588 4.57106 9.65588 4.91816L9.65588 13.1403C9.65583 13.4875 9.37449 13.7688 9.02733 13.7689C8.85389 13.7689 8.69678 13.6985 8.583 13.5848Z" fill="#AAAAAA" />-->
<!--														</svg>-->
<!--													цей варіант якщо користувач додав другу картку -->
<!--													<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--													  <path d="M5.49149 6.12273C5.49149 5.9619 5.55285 5.801 5.67557 5.67827C5.92109 5.43275 6.31895 5.43275 6.56447 5.67828L12.3784 11.4922C12.6239 11.7377 12.6239 12.1356 12.3784 12.3811C12.133 12.6266 11.735 12.6266 11.4896 12.3811L5.67565 6.56718C5.55285 6.44446 5.49149 6.28355 5.49149 6.12273Z" fill="#AAAAAA" />-->
<!--													  <path d="M5.49157 11.9367C5.49157 11.7758 5.55293 11.6149 5.67565 11.4922L11.4896 5.67828C11.735 5.43276 12.133 5.43276 12.3784 5.67828C12.6239 5.92372 12.6239 6.32167 12.3784 6.56711L6.56448 12.381C6.31896 12.6265 5.9211 12.6265 5.67557 12.381C5.55293 12.2584 5.49157 12.0976 5.49157 11.9367Z" fill="#AAAAAA" />-->
<!--													</svg>-->
<!--													</button>-->
<!--												</div>-->
<!--	                                            <div class="info-avatar">-->
<!--	                                                <img src="./img/icon/default-avatar-table.svg" alt="">-->
<!--	                                            </div>-->
<!--	                                            <div class="info-contacts">-->
<!--	                                                <p class="info-contacts-name" data-hover-contact>Федотов Василий</p>-->
<!--	                                                <p class="info-description">Продавец, Покупатель, Арендодат...</p>-->
<!--	                                                <a href="tel:+381231257869" class="info-contacts-tel">+38 (123) 125 - 78 - 69</a>-->
<!--	                                            </div>-->
<!--	                                            <div class="info-links">-->
<!--		                                            <a href="https://wa.me/380XXXXXXXXX">-->
<!--		                                                <img src="./img/icon/icon-table/cnapchat.svg" alt="">-->
<!--													</a>-->
<!--		                                            <a href="viber://chat?number=%2B380XXXXXXXXX">-->
<!--		                                                <img src="./img/icon/icon-table/viber.svg" alt="">-->
<!--													</a>-->
<!--		                                            <a href="https://t.me/+380XXXXXXXXX">-->
<!--		                                                <img src="./img/icon/icon-table/tg.svg" alt="">-->
<!--													</a>-->
<!--												</div>-->
<!--											</li>-->
<!--										</ul>-->
                                    </div>
                                    <div class="filter-tags">
                                        <div class="badge rounded-pill qwe1">Параметр из фильтра</div>
                                        <div class="badge rounded-pill qwe2">Параметр из фильтра</div>
                                        <div class="badge rounded-pill qwe2">Параметр из фильтра</div>
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
                                <p class="info-footer-data">Сделки: <button class="info-footer-btn" type="button">30</button></p>
                                <p class="info-footer-data">Дубликаты: <button class="info-footer-btn btn-others" type="button">3</button></p>
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
			// клас last-update у табличці нище з'являеться лише тоді коли довго не оновлювався об'єкт і перший td
			const othersTable = `
            <div class="table-for-others">
                <table id="example2" style="width:100%;">
                    <tbody>
                        <tr class="last-update">
                        	<td>
                        		<span class="last-update-item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip-red" data-bs-title="Обновлено более 30 дней назад"></span>
							</td>
                            <td>
                                <div class="tbody-wrapper location">
                                    <p>Южная Пальмира</p>
                                    <p>Генуэзская/Посмитного</p>
                                    <span>Аркадия, Одесса, Одесская обл</span>
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
                                    <p>4/25</p>
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
                                <div class="tbody-wrapper contact">
                                    <p data-hover-agent>Фамилия Имя</p>
                                    <p data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Real Estate Name">Real Estate Name</p>
                                    <a href="tel:380968796542">+380968796542</a>
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper block-actions">
                                    <a href="#" class="btn mail-link" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Написать">
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
                         <tr >
                        	<td></td>
                            <td>
                                <div class="tbody-wrapper location">
                                    <p>Южная Пальмира</p>
                                    <p>Генуэзская/Посмитного</p>
                                    <span>Аркадия, Одесса, Одесская обл</span>
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
                                    <p>4/25</p>
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
                                <div class="tbody-wrapper contact">
                                    <p data-hover-agent>Фамилия Имя</p>
                                    <p data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Real Estate Name">Real Estate Name</p>
                                    <a href="tel:380968796542">+380968796542</a>
                                </div>
                            </td>
                            <td>
                                <div class="tbody-wrapper block-actions">
                                    <a href="#" class="btn mail-link" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Написать">
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
                <td colspan="10">
                    <div class="tbody-dop-info">
                        <div class="info-main">
                            <div class="info-main-left">
                                <div class="info-main-left-wrapper">
                                    <div class="description">
                                        <h2 class="description-title">Заголовок текст 1 к кв пл Толбухина срочно</h2>
                                        <p class="description-text">
                                            Отличная квартира сдается длительно порядочным людям. Евроремонт свежий. Есть
                                            вся мебель и техника и еще описание
                                            <span class="more-text" style="display: none;">
                                                Полное описание квартиры с деталями, которые скрыты по умолчанию.
                                            </span>
                                            <button class="btn btn-show-text2"  type="button">Ещё</button>
                                        </p>
                                        <p class="description-note">
                                            <strong>Примечание для агентов:</strong>
                                            <span>Текст примечание для агентов Отличная квартира сдается длительно порядочным людям. Евроремонт свежий. Есть вся мебель и техника и еще описание</span>
                                        </p>
                                        <p class="description-note">
                                            <strong>Заметка:</strong>
                                            <span>Покупает тому-то, продает свою там-то, звонить тогда-то текст личной заметки</span>
                                        </p>
                                    </div>
                                    <ul class="block-info">
                                        <li class="block-info-item">
	                                        <div class="info-title-wrapper">
	                                            <h2 class="info-title">Клиент</h2>
	                                            <button class="btn  btn-edit-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
		                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
													  <path d="M2.33398 10.9996H5.16065C5.24839 11.0001 5.33536 10.9833 5.41659 10.9501C5.49781 10.917 5.57169 10.8681 5.63398 10.8063L10.2473 6.1863L12.1406 4.33297C12.2031 4.27099 12.2527 4.19726 12.2866 4.11602C12.3204 4.03478 12.3378 3.94764 12.3378 3.85963C12.3378 3.77163 12.3204 3.68449 12.2866 3.60325C12.2527 3.52201 12.2031 3.44828 12.1406 3.3863L9.31398 0.5263C9.25201 0.463815 9.17828 0.414219 9.09704 0.380373C9.0158 0.346527 8.92866 0.329102 8.84065 0.329102C8.75264 0.329102 8.66551 0.346527 8.58427 0.380373C8.50303 0.414219 8.42929 0.463815 8.36732 0.5263L6.48732 2.41297L1.86065 7.03297C1.79886 7.09526 1.74998 7.16914 1.7168 7.25036C1.68363 7.33159 1.66681 7.41856 1.66732 7.5063V10.333C1.66732 10.5098 1.73756 10.6793 1.86258 10.8044C1.9876 10.9294 2.15717 10.9996 2.33398 10.9996ZM8.84065 1.93963L10.7273 3.8263L9.78065 4.77297L7.89398 2.8863L8.84065 1.93963ZM3.00065 7.77963L6.95398 3.8263L8.84065 5.71297L4.88732 9.6663H3.00065V7.77963ZM13.0007 12.333H1.00065C0.82384 12.333 0.654271 12.4032 0.529246 12.5282C0.404222 12.6533 0.333984 12.8228 0.333984 12.9996C0.333984 13.1764 0.404222 13.346 0.529246 13.471C0.654271 13.5961 0.82384 13.6663 1.00065 13.6663H13.0007C13.1775 13.6663 13.347 13.5961 13.4721 13.471C13.5971 13.346 13.6673 13.1764 13.6673 12.9996C13.6673 12.8228 13.5971 12.6533 13.4721 12.5282C13.347 12.4032 13.1775 12.333 13.0007 12.333Z" fill="#AAAAAA" />
													</svg>
												</button>
	                                            <button class="btn  btn-add-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
<!--		                                       		<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--													  <path d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z" fill="#AAAAAA" />-->
<!--													  <path d="M8.583 13.5848C8.46922 13.471 8.39889 13.3139 8.39889 13.1403L8.39889 4.91816C8.39883 4.571 8.68022 4.28961 9.02739 4.28967C9.37449 4.28967 9.65588 4.57106 9.65588 4.91816L9.65588 13.1403C9.65583 13.4875 9.37449 13.7688 9.02733 13.7689C8.85389 13.7689 8.69678 13.6985 8.583 13.5848Z" fill="#AAAAAA" />-->
<!--													</svg>-->
<!--													цей варіант якщо користувач додав другу картку -->
													<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
													  <path d="M5.49149 6.12273C5.49149 5.9619 5.55285 5.801 5.67557 5.67827C5.92109 5.43275 6.31895 5.43275 6.56447 5.67828L12.3784 11.4922C12.6239 11.7377 12.6239 12.1356 12.3784 12.3811C12.133 12.6266 11.735 12.6266 11.4896 12.3811L5.67565 6.56718C5.55285 6.44446 5.49149 6.28355 5.49149 6.12273Z" fill="#AAAAAA" />
													  <path d="M5.49157 11.9367C5.49157 11.7758 5.55293 11.6149 5.67565 11.4922L11.4896 5.67828C11.735 5.43276 12.133 5.43276 12.3784 5.67828C12.6239 5.92372 12.6239 6.32167 12.3784 6.56711L6.56448 12.381C6.31896 12.6265 5.9211 12.6265 5.67557 12.381C5.55293 12.2584 5.49157 12.0976 5.49157 11.9367Z" fill="#AAAAAA" />
													</svg>
												</button>
											</div>
                                            <div class="info-avatar">
                                                <img src="./img/icon/default-avatar-table.svg" alt="">
                                            </div>
                                            <div class="info-contacts">
                                                <p class="info-contacts-name" data-hover-contact>Федотов Василий</p>
                                                <p class="info-description">Продавец, Покупатель, Арендодат...</p>
                                                <a href="tel:+381231257869" class="info-contacts-tel">+38 (123) 125 - 78 - 69</a>
                                            </div>
										</li>
										<li class="block-info-item">
	                                        <div class="info-title-wrapper">
	                                            <h2 class="info-title">Клиент</h2>
	                                            <button class="btn  btn-edit-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
		                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
													  <path d="M2.33398 10.9996H5.16065C5.24839 11.0001 5.33536 10.9833 5.41659 10.9501C5.49781 10.917 5.57169 10.8681 5.63398 10.8063L10.2473 6.1863L12.1406 4.33297C12.2031 4.27099 12.2527 4.19726 12.2866 4.11602C12.3204 4.03478 12.3378 3.94764 12.3378 3.85963C12.3378 3.77163 12.3204 3.68449 12.2866 3.60325C12.2527 3.52201 12.2031 3.44828 12.1406 3.3863L9.31398 0.5263C9.25201 0.463815 9.17828 0.414219 9.09704 0.380373C9.0158 0.346527 8.92866 0.329102 8.84065 0.329102C8.75264 0.329102 8.66551 0.346527 8.58427 0.380373C8.50303 0.414219 8.42929 0.463815 8.36732 0.5263L6.48732 2.41297L1.86065 7.03297C1.79886 7.09526 1.74998 7.16914 1.7168 7.25036C1.68363 7.33159 1.66681 7.41856 1.66732 7.5063V10.333C1.66732 10.5098 1.73756 10.6793 1.86258 10.8044C1.9876 10.9294 2.15717 10.9996 2.33398 10.9996ZM8.84065 1.93963L10.7273 3.8263L9.78065 4.77297L7.89398 2.8863L8.84065 1.93963ZM3.00065 7.77963L6.95398 3.8263L8.84065 5.71297L4.88732 9.6663H3.00065V7.77963ZM13.0007 12.333H1.00065C0.82384 12.333 0.654271 12.4032 0.529246 12.5282C0.404222 12.6533 0.333984 12.8228 0.333984 12.9996C0.333984 13.1764 0.404222 13.346 0.529246 13.471C0.654271 13.5961 0.82384 13.6663 1.00065 13.6663H13.0007C13.1775 13.6663 13.347 13.5961 13.4721 13.471C13.5971 13.346 13.6673 13.1764 13.6673 12.9996C13.6673 12.8228 13.5971 12.6533 13.4721 12.5282C13.347 12.4032 13.1775 12.333 13.0007 12.333Z" fill="#AAAAAA" />
													</svg>
												</button>
	                                            <button class="btn  btn-add-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
		                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
													  <path d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z" fill="#AAAAAA" />
													  <path d="M8.583 13.5848C8.46922 13.471 8.39889 13.3139 8.39889 13.1403L8.39889 4.91816C8.39883 4.571 8.68022 4.28961 9.02739 4.28967C9.37449 4.28967 9.65588 4.57106 9.65588 4.91816L9.65588 13.1403C9.65583 13.4875 9.37449 13.7688 9.02733 13.7689C8.85389 13.7689 8.69678 13.6985 8.583 13.5848Z" fill="#AAAAAA" />
													</svg>
<!--													цей варіант якщо користувач додав другу картку -->
<!--													<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--													  <path d="M5.49149 6.12273C5.49149 5.9619 5.55285 5.801 5.67557 5.67827C5.92109 5.43275 6.31895 5.43275 6.56447 5.67828L12.3784 11.4922C12.6239 11.7377 12.6239 12.1356 12.3784 12.3811C12.133 12.6266 11.735 12.6266 11.4896 12.3811L5.67565 6.56718C5.55285 6.44446 5.49149 6.28355 5.49149 6.12273Z" fill="#AAAAAA" />-->
<!--													  <path d="M5.49157 11.9367C5.49157 11.7758 5.55293 11.6149 5.67565 11.4922L11.4896 5.67828C11.735 5.43276 12.133 5.43276 12.3784 5.67828C12.6239 5.92372 12.6239 6.32167 12.3784 6.56711L6.56448 12.381C6.31896 12.6265 5.9211 12.6265 5.67557 12.381C5.55293 12.2584 5.49157 12.0976 5.49157 11.9367Z" fill="#AAAAAA" />-->
<!--													</svg>-->
												</button>
											</div>
                                            <div class="info-avatar">
                                                <img src="./img/icon/default-avatar-table.svg" alt="">
                                            </div>
                                            <div class="info-contacts">
                                                <p class="info-contacts-name" data-hover-contact>Федотов Василий</p>
                                                <p class="info-description">Продавец, Покупатель, Арендодат...</p>
                                                <a href="tel:+381231257869" class="info-contacts-tel">+38 (123) 125 - 78 - 69</a>
                                            </div>
										</li>
									</ul>
                                </div>
                                <div class="filter-tags">
                                    <div class="badge rounded-pill qwe1">Параметр из фильтра</div>
                                    <div class="badge rounded-pill qwe2">Параметр из фильтра</div>
                                    <div class="badge rounded-pill qwe2">Параметр из фильтра</div>
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
		// 08.05.2025
		initPhotoHoverPreview();
	});
	
	// Викликаємо ініціалізацію Tooltip після динамічного додавання рядків
	$('#example tbody').on('click', '.details-control, .details-control-dop, #btn-others', function () {
		setTimeout(() => {
			initTooltips();
		}, 100);
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
	
});