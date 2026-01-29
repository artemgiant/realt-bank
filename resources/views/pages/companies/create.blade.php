<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Page-create-agency</title>
    <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/lib/select2.min.css">
    <link rel="stylesheet" href="./css/lib/bootstrap.v5.3.3.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css">
    <link rel="stylesheet" href="./css/lib/fancybox.min.css">
    <link rel="stylesheet" href="./css/lib/data-range-picker.min.css">
    <!-- для роботи Гео Модалки 27.08.2025 -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
    <!-- для роботи Гео Модалки 27.08.2025 -->
    <link rel="stylesheet" href="./css/pages/page-create-agency.min.css">
</head>
<body class="d-flex flex-column min-vh-100">
<main class="wrapper">
    <!-- початок side-bar	-->
    <aside class="sidebar">
        <nav class="nav">
            <ul class="nav-list">
                <li class="nav-list-item">
                    <a href="" class="nav-list-link sidebar-logo">
                        <picture><source srcset="./img/icon/side-bar/logo-F.svg" type="image/webp"><img src="./img/icon/side-bar/logo-F.svg" alt=""></picture>
                    </a>
                </li>
                <li class="nav-list-item">
                    <a class="nav-list-link active" href="./page-home.html">
					<span class="nav-list-icon">
						<picture><source srcset="./img/icon/side-bar/Finanse.svg" type="image/webp"><img src="./img/icon/side-bar/Finanse.svg" alt=""></picture>
					</span>
                        <span class="nav-list-text">
						Недвижимость
					</span>
                    </a>
                </li>
                <li class="nav-list-item">
                    <a class="nav-list-link" href="#">
					<span class="nav-list-icon">
						<picture><source srcset="./img/icon/side-bar/Deals.svg" type="image/webp"><img src="./img/icon/side-bar/Deals.svg" alt=""></picture>
						<span class="my-badge">
							15
						</span>
					</span>
                        <span class="nav-list-text">
						Сделки
					</span>
                    </a>
                </li>
                <li class="nav-list-item">
                    <a class="nav-list-link" href="#">
					<span class="nav-list-icon">
						<picture><source srcset="./img/icon/side-bar/Tasks.svg" type="image/webp"><img src="./img/icon/side-bar/Tasks.svg" alt=""></picture>
						<span class="my-badge">
							233
						</span>
					</span>
                        <span class="nav-list-text">
						Задачи
					</span>
                    </a>
                </li>
                <li class="nav-list-item">
                    <a class="nav-list-link" href="./page-company-agents.html">
					<span class="nav-list-icon">
						<picture><source srcset="./img/icon/side-bar/Company1.svg" type="image/webp"><img src="./img/icon/side-bar/Company1.svg" alt=""></picture>
					</span>
                        <span class="nav-list-text">
						Агентство
					</span>
                    </a>
                </li>
            </ul>
            <ul class="nav-info">
                <li class="nav-info-item">
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            RU
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">UA</a></li>
                            <li><a class="dropdown-item" href="#">RU</a></li>
                            <li><a class="dropdown-item" href="#">EN</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-info-item">
                    <a class="nav-info-link position-relative" href="#">
                        <picture><source srcset="./img/icon/side-bar/mail-white.svg" type="image/webp"><img src="./img/icon/side-bar/mail-white.svg" alt=""></picture>
                        <span class="my-badge"></span>
                    </a>
                </li>
                <li class="nav-info-item">
                    <a class="nav-info-link" href="./page-company-settings.html">
                        <picture><source srcset="./img/icon/side-bar/settings-white.svg" type="image/webp"><img src="./img/icon/side-bar/settings-white.svg" alt=""></picture>
                    </a>
                </li>
                <li class="nav-info-item">
                    <a class="nav-info-link" href="./page-my-profile.html">
                        <picture><source srcset="./img/icon/side-bar/default-avatar.svg" type="image/webp"><img src="./img/icon/side-bar/default-avatar.svg" alt=""></picture>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    <!-- кінець side-bar	-->
    <!-- початок main	-->
    <div class="container-fluid">
        <div class="block">
            <header class="header-wrapper">
                <h1 class="header-title">
                    Добавление агентства
                </h1>
            </header>

            <div class="block-info-list">
                <ul class="block-info">
                    <li class="block-info-item">
                        <div class="info-title-wrapper">
                            <h2 class="info-title">Контакт</h2>
                            <button class="btn  btn-edit-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.33398 10.9996H5.16065C5.24839 11.0001 5.33536 10.9833 5.41659 10.9501C5.49781 10.917 5.57169 10.8681 5.63398 10.8063L10.2473 6.1863L12.1406 4.33297C12.2031 4.27099 12.2527 4.19726 12.2866 4.11602C12.3204 4.03478 12.3378 3.94764 12.3378 3.85963C12.3378 3.77163 12.3204 3.68449 12.2866 3.60325C12.2527 3.52201 12.2031 3.44828 12.1406 3.3863L9.31398 0.5263C9.25201 0.463815 9.17828 0.414219 9.09704 0.380373C9.0158 0.346527 8.92866 0.329102 8.84065 0.329102C8.75264 0.329102 8.66551 0.346527 8.58427 0.380373C8.50303 0.414219 8.42929 0.463815 8.36732 0.5263L6.48732 2.41297L1.86065 7.03297C1.79886 7.09526 1.74998 7.16914 1.7168 7.25036C1.68363 7.33159 1.66681 7.41856 1.66732 7.5063V10.333C1.66732 10.5098 1.73756 10.6793 1.86258 10.8044C1.9876 10.9294 2.15717 10.9996 2.33398 10.9996ZM8.84065 1.93963L10.7273 3.8263L9.78065 4.77297L7.89398 2.8863L8.84065 1.93963ZM3.00065 7.77963L6.95398 3.8263L8.84065 5.71297L4.88732 9.6663H3.00065V7.77963ZM13.0007 12.333H1.00065C0.82384 12.333 0.654271 12.4032 0.529246 12.5282C0.404222 12.6533 0.333984 12.8228 0.333984 12.9996C0.333984 13.1764 0.404222 13.346 0.529246 13.471C0.654271 13.5961 0.82384 13.6663 1.00065 13.6663H13.0007C13.1775 13.6663 13.347 13.5961 13.4721 13.471C13.5971 13.346 13.6673 13.1764 13.6673 12.9996C13.6673 12.8228 13.5971 12.6533 13.4721 12.5282C13.347 12.4032 13.1775 12.333 13.0007 12.333Z" fill="#AAAAAA"></path>
                                </svg>
                            </button>
                            <button class="btn  btn-add-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z" fill="#AAAAAA"></path>
                                    <path d="M8.583 13.5848C8.46922 13.471 8.39889 13.3139 8.39889 13.1403L8.39889 4.91816C8.39883 4.571 8.68022 4.28961 9.02739 4.28967C9.37449 4.28967 9.65588 4.57106 9.65588 4.91816L9.65588 13.1403C9.65583 13.4875 9.37449 13.7688 9.02733 13.7689C8.85389 13.7689 8.69678 13.6985 8.583 13.5848Z" fill="#AAAAAA"></path>
                                    <!--											тут закоментований мінус-->
                                    <!--											<path-->
                                    <!--												d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z"-->
                                    <!--												fill="#AAAAAA"/>-->
                                </svg>
                            </button>
                        </div>
                        <div class="info-avatar">
                            <picture><source srcset="./img/icon/default-avatar-table.svg" type="image/webp"><img src="./img/icon/default-avatar-table.svg" alt=""></picture>
                        </div>
                        <div class="info-contacts">
                            <p class="info-contacts-name">Василий Федотов</p>
                            <p class="info-description">Real Estate Name</p>
                            <a href="tel:+381231257869" class="info-contacts-tel">+38 (123) 125 - 78 - 69</a>
                        </div>
                        <div class="info-links">
                            <a href="https://wa.me/380XXXXXXXXX">
                                <picture><source srcset="./img/icon/icon-table/cnapchat.svg" type="image/webp"><img src="./img/icon/icon-table/cnapchat.svg" alt=""></picture>
                            </a>
                            <a href="viber://chat?number=%2B380XXXXXXXXX">
                                <picture><source srcset="./img/icon/icon-table/viber.svg" type="image/webp"><img src="./img/icon/icon-table/viber.svg" alt=""></picture>
                            </a>
                            <a href="https://t.me/+380XXXXXXXXX">
                                <picture><source srcset="./img/icon/icon-table/tg.svg" type="image/webp"><img src="./img/icon/icon-table/tg.svg" alt=""></picture>
                            </a>
                            <a href="#">
                                <picture><source srcset="./img/icon/icon-table/instagram.svg" type="image/webp"><img src="./img/icon/icon-table/instagram.svg" alt=""></picture>
                            </a>
                            <a href="#">
                                <picture><source srcset="./img/icon/icon-table/facebook.svg" type="image/webp"><img src="./img/icon/icon-table/facebook.svg" alt=""></picture>
                            </a>
                            <a href="#">
                                <picture><source srcset="./img/icon/icon-table/tiktok.svg" type="image/webp"><img src="./img/icon/icon-table/tiktok.svg" alt=""></picture>
                            </a>
                            <a href="#">
                                <picture><source srcset="./img/icon/icon-table/youtube.svg" type="image/webp"><img src="./img/icon/icon-table/youtube.svg" alt=""></picture>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="block-all-info">
                <h3 class="block-title">
                    <span>Общая информация</span>
                </h3>
                <div class="block-row">
                    <div class="item">
                        <div class="tab-the-name">
                            <ul class="nav nav-tabs" id="tab-the-name" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ua-tab" data-bs-toggle="tab"
                                            data-bs-target="#ua-tab-pane" type="button" role="tab"
                                            aria-controls="ua-tab-pane" aria-selected="false">UA
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="ru-tab" data-bs-toggle="tab"
                                            data-bs-target="#ru-tab-pane" type="button" role="tab"
                                            aria-controls="ru-tab-pane" aria-selected="true">RU
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="en-tab" data-bs-toggle="tab"
                                            data-bs-target="#en-tab-pane" type="button" role="tab"
                                            aria-controls="en-tab-pane" aria-selected="false">EN
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade" id="ua-tab-pane" role="tabpanel"
                                     aria-labelledby="ua-tab" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
                                            <label class="green" for="name-agency-ua">Назва агенції</label>
                                            <div class="item-inputText-wrapper">
                                                <input class="item-inputText" type="text"
                                                       data-input-lang="ua" id="name-agency-ua" autocomplete="off"
                                                       name="text_advertising-ua"
                                                       placeholder="Назва">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show active" id="ru-tab-pane" role="tabpanel"
                                     aria-labelledby="ru-tab" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
                                            <label class="green" for="name-agency-ru">Название агентства</label>
                                            <div class="item-inputText-wrapper">
                                                <input class="item-inputText" type="text"
                                                       data-input-lang="ru" id="name-agency-ru" autocomplete="off"
                                                       name="text_advertising-ru"
                                                       placeholder="Название">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="en-tab-pane" role="tabpanel"
                                     aria-labelledby="en-tab" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
                                            <label class="green" for="name-agency-en">The name of the agency</label>
                                            <div class="item-inputText-wrapper">
                                                <input class="item-inputText" type="text"
                                                       data-input-lang="en" id="name-agency-en" autocomplete="off"
                                                       name="text_advertising-en"
                                                       placeholder="The name">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item w50">
                        <div class="add_new-tel">
                            <button type="button" class="btn btn-new-tel">
                                <svg width="11" height="11" viewBox="0 0 11 11" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                            d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z"
                                            fill="#3585F5"/>
                                    <path
                                            d="M4.583 9.58476C4.46922 9.47098 4.39889 9.31387 4.39889 9.14032L4.39889 0.918164C4.39883 0.571001 4.68022 0.289614 5.02739 0.28967C5.37449 0.28967 5.65588 0.571056 5.65588 0.918164L5.65588 9.14032C5.65583 9.48748 5.37449 9.76881 5.02733 9.76887C4.85389 9.76887 4.69678 9.69853 4.583 9.58476Z"
                                            fill="#3585F5"/>
                                </svg>
                            </button>
                        </div>
                        <span class="item-label">
				Локация
			</span>
                        <div class="my-dropdown">
                            <div class="my-dropdown-input-wrapper">
                                <!-- 05.06.2025		-->
                                <button class="my-dropdown-geo-btn" data-bs-toggle="modal" data-bs-target="#geoModal">
                                    <picture><source srcset="./img/icon/geo.svg" type="image/webp"><img src="./img/icon/geo.svg" alt=""></picture>
                                </button>
                                <!-- 05.06.2025		-->

                                <label class="my-dropdown-label">
                                    <input class="my-dropdown-input" type="text" autocomplete="off" placeholder="Введите название">
                                </label>
                                <!--		28.03.2025 оновив-->
                                <button class="my-dropdown-btn arrow-down" id="btn-open-menu" type="button">
                                    <picture><source srcset="./img/icon/arrow-right-white.svg" type="image/webp"><img src="./img/icon/arrow-right-white.svg" alt=""></picture>
                                </button>
                                <!--		28.03.2025 оновив-->
                            </div>
                            <div class="my-dropdown-list-wrapper" style="display: none">
                                <div class="my-dropdown-list">
                                    <div class="scroller">
                                        <div class="my-dropdown-item">
                                            <label class="my-dropdown-item-label-radio">
                                                <input class="my-dropdown-item-radio" type="radio" name="country">
                                                <span class="my-dropdown-item-radio-text">
							Україна (<span>24</span>)
						</span>
                                            </label>
                                            <div class="my-dropdown-next-list" style="display: none">
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-radio">
                                                        <input class="my-dropdown-item-radio" type="radio" name="district">
                                                        <span class="my-dropdown-item-radio-text">
									Дніпропетровська обл. (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-radio">
                                                        <input class="my-dropdown-item-radio" type="radio" name="district">
                                                        <span class="my-dropdown-item-radio-text">
									Одеська обл. (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="my-dropdown-item">
                                            <label class="my-dropdown-item-label-radio">
                                                <input class="my-dropdown-item-radio" type="radio" name="country">
                                                <span class="my-dropdown-item-radio-text">
						Великобритания (<span>24</span>)
					</span>
                                            </label>
                                            <div class="my-dropdown-next-list" style="display: none">
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-radio">
                                                        <input class="my-dropdown-item-radio" type="radio" name="district">
                                                        <span class="my-dropdown-item-radio-text">
									Дніпропетровська обл. (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-radio">
                                                        <input class="my-dropdown-item-radio" type="radio" name="district">
                                                        <span class="my-dropdown-item-radio-text">
									Одеська обл. (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="my-dropdown-list second" style="display: none">
                                    <div class="scroller">
                                        <div class="my-dropdown-item">
                                            <label class="my-dropdown-item-label-checkbox">
                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                <span class="my-dropdown-item-checkbox-text">
							Дніпро (<span>24</span>)
						</span>
                                            </label>
                                            <div class="my-dropdown-next-list" style="display: none">
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									АНД район (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									Індустріальний район (<span>24</span>)
								</span>
                                                    </label>
                                                    <div class="my-dropdown-next-next-list" style="display: none">
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
											Лівобережний 3 (<span>24</span>)
										</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
											Лівобережний 2 (<span>24</span>)
										</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
											Лівобережний 1 (<span>24</span>)
										</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									Центральний район (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									Новокадацький район (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									Шевченківський район (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="my-dropdown-item">
                                            <label class="my-dropdown-item-label-checkbox">
                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                <span class="my-dropdown-item-checkbox-text">
						Одесса (<span>24</span>)
					</span>
                                            </label>
                                            <div class="my-dropdown-next-list" style="display: none">
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									АНД район (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									Індустріальний район (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									Центральний район (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									Новокадацький район (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									Шевченківський район (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="my-dropdown-item">
                                            <label class="my-dropdown-item-label-checkbox">
                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                <span class="my-dropdown-item-checkbox-text">
						Київ (<span>24</span>)
					</span>
                                            </label>
                                        </div>
                                        <div class="my-dropdown-item">
                                            <label class="my-dropdown-item-label-checkbox">
                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                <span class="my-dropdown-item-checkbox-text">
						Харків (<span>24</span>)
					</span>
                                            </label>
                                            <div class="my-dropdown-next-list" style="display: none">
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									АНД район (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									Індустріальний район (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									Центральний район (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									Новокадацький район (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
									Шевченківський район (<span>24</span>)
								</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="my-dropdown-search-wrapper" style="display: none">
                                <div class="my-dropdown-search-list">
                                    <div class="scroller">
                                        <div class="my-dropdown-search-item">
                                            <div class="eqweqw">
                                                Одесская обл (24)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item">
			<span>
				<label class="item-label" for="site-agency">Сайт агентства</label>
			</span>
                        <input class="item-inputText" id="site-agency" type="text" autocomplete="off" placeholder="https://linkname.com">
                    </div>
                </div>
                <div class="block-row">
                    <div class="item">
                        <div class="item">
				<span>
					<label class="item-label" for="code-EDRPOU-TIN">КОД ЕГРПОУ/ИНН</label>
				</span>
                            <input class="item-inputText" id="code-EDRPOU-TIN" type="text" autocomplete="off"
                                   placeholder="1234567890">
                        </div>
                        <div class="item">
				<span class="label">
					Логотип
				</span>
                            <div class="photo-info-list-wrapper">
                                <ul class="photo-info-list">
                                    <li class="photo-info-btn-wrapper">
                                        <label class="photo-info-btn" for="loading-photo">
                                            <input type="file" id="loading-photo" name="loading-photo" accept="image/png, image/jpg, image/jpeg, image/heic">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.50725 13.2938C7.814 13.9437 6.89515 14.2986 5.945 14.2833C4.99486 14.2679 4.08791 13.8837 3.41597 13.2117C2.74403 12.5398 2.35977 11.6329 2.34446 10.6827C2.32914 9.73256 2.68398 8.81371 3.33392 8.12046L9.17392 2.28713C9.52109 1.94269 9.96235 1.70858 10.4422 1.61425C10.9221 1.51991 11.4191 1.56956 11.8708 1.75695C12.3226 1.94433 12.7088 2.2611 12.981 2.6674C13.2532 3.0737 13.3992 3.55141 13.4006 4.04046C13.4002 4.36567 13.3352 4.68757 13.2093 4.98743C13.0834 5.28729 12.8992 5.55912 12.6672 5.78713L7.11392 11.3338C6.94029 11.4722 6.72193 11.5421 6.50022 11.5302C6.27851 11.5183 6.06887 11.4254 5.91105 11.2692C5.75324 11.1131 5.65821 10.9044 5.64399 10.6828C5.62977 10.4613 5.69735 10.2422 5.83392 10.0671L11.3939 4.50713L10.4472 3.56713L4.88725 9.12713C4.486 9.55082 4.26593 10.1144 4.27387 10.6978C4.2818 11.2813 4.51712 11.8387 4.92974 12.2513C5.34236 12.6639 5.89971 12.8992 6.4832 12.9072C7.06668 12.9151 7.63022 12.695 8.05392 12.2938L13.6206 6.73379C14.3367 6.01859 14.7393 5.04822 14.7399 4.03615C14.7406 3.02408 14.3391 2.05321 13.6239 1.33713C12.9087 0.621043 11.9383 0.218399 10.9263 0.217774C9.9142 0.217149 8.94333 0.618593 8.22725 1.33379L2.38725 7.18046C1.4841 8.1234 0.986222 9.38258 1.00029 10.6882C1.01436 11.9938 1.53926 13.2419 2.46251 14.1652C3.38577 15.0885 4.63393 15.6133 5.93953 15.6274C7.24513 15.6415 8.50431 15.1436 9.44725 14.2405L14.7272 8.95379L13.7872 8.00046L8.50725 13.2938Z" fill="#3585F5" />
                                            </svg>
                                            <span>
									Загрузить лого
								</span>
                                        </label>
                                    </li>
                                </ul>
                                <div class="error-container"></div>
                            </div>
                        </div>
                    </div>
                    <div class="item w75">
                        <div class="tab-the-name">
                            <ul class="nav nav-tabs" id="tab-about-agency" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ua-tab-about-agency" data-bs-toggle="tab"
                                            data-bs-target="#ua-tab-pane-about-agency" type="button" role="tab"
                                            aria-controls="ua-tab-pane-about-agency" aria-selected="false">UA
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="ru-tab-about-agency" data-bs-toggle="tab"
                                            data-bs-target="#ru-tab-pane-about-agency" type="button" role="tab"
                                            aria-controls="ru-tab-pane-about-agency" aria-selected="true">RU
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="en-tab-about-agency" data-bs-toggle="tab"
                                            data-bs-target="#en-tab-pane-about-agency" type="button" role="tab"
                                            aria-controls="en-tab-pane-about-agency" aria-selected="false">EN
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button id="generation-ai-about-agency" class="nav-link ai" type="button">
                                        <span>AI Text</span>
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade" id="ua-tab-pane-about-agency" role="tabpanel"
                                     aria-labelledby="ua-tab-about-agency" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
							<span>
								<label for="about-agency-ua">Про агенцію</label>
							</span>
                                            <div class="item-inputText-wrapper">
								<textarea class="item-textareaText" type="text"
                                          data-input-lang="ua" id="about-agency-ua"
                                          autocomplete="off" name="about-agency-ua"
                                          placeholder="Введіть текст"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show active" id="ru-tab-pane-about-agency" role="tabpanel"
                                     aria-labelledby="ru-tab-about-agency" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
								<span>
									<label for="about-agency-ru">Об агентстве</label>
								</span>
                                            <div class="item-inputText-wrapper">
									<textarea class="item-textareaText" type="text"
                                              data-input-lang="ru" id="about-agency-ru"
                                              autocomplete="off" name="about-agency-ru"
                                              placeholder="Введите текст"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="en-tab-pane-about-agency" role="tabpanel"
                                     aria-labelledby="en-tab-about-agency" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
							<span>
								<label for="about-agency-en">About agency</label>
							</span>
                                            <div class="item-inputText-wrapper">
								<textarea class="item-textareaText" type="text"
                                          data-input-lang="en" id="about-agency-en"
                                          autocomplete="off" name="about-agency-en"
                                          placeholder="Enter text"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="block-offices">
                <h3 class="block-title">
                    <span>Офисы</span>
                </h3>
                <div class="block-offices-list">
                    <div class="block-offices-item">
                        <div class="block-row">
                            <div class="item">
                                <div class="add_new-tel">
                                    <button type="button" class="btn btn-new-tel">
                                        <svg width="11" height="11" viewBox="0 0 11 11" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                    d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z"
                                                    fill="#3585F5"/>
                                            <path
                                                    d="M4.583 9.58476C4.46922 9.47098 4.39889 9.31387 4.39889 9.14032L4.39889 0.918164C4.39883 0.571001 4.68022 0.289614 5.02739 0.28967C5.37449 0.28967 5.65588 0.571056 5.65588 0.918164L5.65588 9.14032C5.65583 9.48748 5.37449 9.76881 5.02733 9.76887C4.85389 9.76887 4.69678 9.69853 4.583 9.58476Z"
                                                    fill="#3585F5"/>
                                        </svg>
                                    </button>
                                </div>
                                <span>
						<label class="item-label green" for="agency-branch">Название офиса</label>
					</span>
                                <input class="item-inputText" id="agency-branch" type="text" autocomplete="off" placeholder="Название">
                            </div>
                            <div class="item w75">
                                <ul class="block-info">
                                    <li class="block-info-item">
                                        <div class="info-title-wrapper">
                                            <h2 class="info-title">Контакт</h2>
                                            <button class="btn  btn-edit-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2.33398 10.9996H5.16065C5.24839 11.0001 5.33536 10.9833 5.41659 10.9501C5.49781 10.917 5.57169 10.8681 5.63398 10.8063L10.2473 6.1863L12.1406 4.33297C12.2031 4.27099 12.2527 4.19726 12.2866 4.11602C12.3204 4.03478 12.3378 3.94764 12.3378 3.85963C12.3378 3.77163 12.3204 3.68449 12.2866 3.60325C12.2527 3.52201 12.2031 3.44828 12.1406 3.3863L9.31398 0.5263C9.25201 0.463815 9.17828 0.414219 9.09704 0.380373C9.0158 0.346527 8.92866 0.329102 8.84065 0.329102C8.75264 0.329102 8.66551 0.346527 8.58427 0.380373C8.50303 0.414219 8.42929 0.463815 8.36732 0.5263L6.48732 2.41297L1.86065 7.03297C1.79886 7.09526 1.74998 7.16914 1.7168 7.25036C1.68363 7.33159 1.66681 7.41856 1.66732 7.5063V10.333C1.66732 10.5098 1.73756 10.6793 1.86258 10.8044C1.9876 10.9294 2.15717 10.9996 2.33398 10.9996ZM8.84065 1.93963L10.7273 3.8263L9.78065 4.77297L7.89398 2.8863L8.84065 1.93963ZM3.00065 7.77963L6.95398 3.8263L8.84065 5.71297L4.88732 9.6663H3.00065V7.77963ZM13.0007 12.333H1.00065C0.82384 12.333 0.654271 12.4032 0.529246 12.5282C0.404222 12.6533 0.333984 12.8228 0.333984 12.9996C0.333984 13.1764 0.404222 13.346 0.529246 13.471C0.654271 13.5961 0.82384 13.6663 1.00065 13.6663H13.0007C13.1775 13.6663 13.347 13.5961 13.4721 13.471C13.5971 13.346 13.6673 13.1764 13.6673 12.9996C13.6673 12.8228 13.5971 12.6533 13.4721 12.5282C13.347 12.4032 13.1775 12.333 13.0007 12.333Z" fill="#AAAAAA"></path>
                                                </svg>
                                            </button>
                                            <button class="btn  btn-add-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z" fill="#AAAAAA"></path>
                                                    <path d="M8.583 13.5848C8.46922 13.471 8.39889 13.3139 8.39889 13.1403L8.39889 4.91816C8.39883 4.571 8.68022 4.28961 9.02739 4.28967C9.37449 4.28967 9.65588 4.57106 9.65588 4.91816L9.65588 13.1403C9.65583 13.4875 9.37449 13.7688 9.02733 13.7689C8.85389 13.7689 8.69678 13.6985 8.583 13.5848Z" fill="#AAAAAA"></path>
                                                    <!--											тут закоментований мінус-->
                                                    <!--											<path-->
                                                    <!--												d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z"-->
                                                    <!--												fill="#AAAAAA"/>-->
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="info-avatar">
                                            <picture><source srcset="./img/icon/default-avatar-table.svg" type="image/webp"><img src="./img/icon/default-avatar-table.svg" alt=""></picture>
                                        </div>
                                        <div class="info-contacts">
                                            <p class="info-contacts-name">Василий Федотов</p>
                                            <p class="info-description">Real Estate Name</p>
                                            <a href="tel:+381231257869" class="info-contacts-tel">+38 (123) 125 - 78 - 69</a>
                                        </div>
                                        <div class="info-links">
                                            <a href="https://wa.me/380XXXXXXXXX">
                                                <picture><source srcset="./img/icon/icon-table/cnapchat.svg" type="image/webp"><img src="./img/icon/icon-table/cnapchat.svg" alt=""></picture>
                                            </a>
                                            <a href="viber://chat?number=%2B380XXXXXXXXX">
                                                <picture><source srcset="./img/icon/icon-table/viber.svg" type="image/webp"><img src="./img/icon/icon-table/viber.svg" alt=""></picture>
                                            </a>
                                            <a href="https://t.me/+380XXXXXXXXX">
                                                <picture><source srcset="./img/icon/icon-table/tg.svg" type="image/webp"><img src="./img/icon/icon-table/tg.svg" alt=""></picture>
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="block-row">
                            <div class="item w50">
			<span>
				<span class="item-label">
					Локация
				</span>
			</span>
                                <div class="my-dropdown">
                                    <div class="my-dropdown-input-wrapper">
                                        <!-- 05.06.2025		-->
                                        <button class="my-dropdown-geo-btn" data-bs-toggle="modal" data-bs-target="#geoModal">
                                            <picture><source srcset="./img/icon/geo.svg" type="image/webp"><img src="./img/icon/geo.svg" alt=""></picture>
                                        </button>
                                        <!-- 05.06.2025		-->

                                        <label class="my-dropdown-label">
                                            <input class="my-dropdown-input" type="text" autocomplete="off" placeholder="Введите название">
                                        </label>
                                        <!--		28.03.2025 оновив-->
                                        <button class="my-dropdown-btn arrow-down" id="btn-open-menu" type="button">
                                            <picture><source srcset="./img/icon/arrow-right-white.svg" type="image/webp"><img src="./img/icon/arrow-right-white.svg" alt=""></picture>
                                        </button>
                                        <!--		28.03.2025 оновив-->
                                    </div>
                                    <div class="my-dropdown-list-wrapper" style="display: none">
                                        <div class="my-dropdown-list">
                                            <div class="scroller">
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-radio">
                                                        <input class="my-dropdown-item-radio" type="radio" name="country">
                                                        <span class="my-dropdown-item-radio-text">
							Україна (<span>24</span>)
						</span>
                                                    </label>
                                                    <div class="my-dropdown-next-list" style="display: none">
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-radio">
                                                                <input class="my-dropdown-item-radio" type="radio" name="district">
                                                                <span class="my-dropdown-item-radio-text">
									Дніпропетровська обл. (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-radio">
                                                                <input class="my-dropdown-item-radio" type="radio" name="district">
                                                                <span class="my-dropdown-item-radio-text">
									Одеська обл. (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-radio">
                                                        <input class="my-dropdown-item-radio" type="radio" name="country">
                                                        <span class="my-dropdown-item-radio-text">
						Великобритания (<span>24</span>)
					</span>
                                                    </label>
                                                    <div class="my-dropdown-next-list" style="display: none">
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-radio">
                                                                <input class="my-dropdown-item-radio" type="radio" name="district">
                                                                <span class="my-dropdown-item-radio-text">
									Дніпропетровська обл. (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-radio">
                                                                <input class="my-dropdown-item-radio" type="radio" name="district">
                                                                <span class="my-dropdown-item-radio-text">
									Одеська обл. (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="my-dropdown-list second" style="display: none">
                                            <div class="scroller">
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
							Дніпро (<span>24</span>)
						</span>
                                                    </label>
                                                    <div class="my-dropdown-next-list" style="display: none">
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									АНД район (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									Індустріальний район (<span>24</span>)
								</span>
                                                            </label>
                                                            <div class="my-dropdown-next-next-list" style="display: none">
                                                                <div class="my-dropdown-item">
                                                                    <label class="my-dropdown-item-label-checkbox">
                                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                        <span class="my-dropdown-item-checkbox-text">
											Лівобережний 3 (<span>24</span>)
										</span>
                                                                    </label>
                                                                </div>
                                                                <div class="my-dropdown-item">
                                                                    <label class="my-dropdown-item-label-checkbox">
                                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                        <span class="my-dropdown-item-checkbox-text">
											Лівобережний 2 (<span>24</span>)
										</span>
                                                                    </label>
                                                                </div>
                                                                <div class="my-dropdown-item">
                                                                    <label class="my-dropdown-item-label-checkbox">
                                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                        <span class="my-dropdown-item-checkbox-text">
											Лівобережний 1 (<span>24</span>)
										</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									Центральний район (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									Новокадацький район (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									Шевченківський район (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
						Одесса (<span>24</span>)
					</span>
                                                    </label>
                                                    <div class="my-dropdown-next-list" style="display: none">
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									АНД район (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									Індустріальний район (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									Центральний район (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									Новокадацький район (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									Шевченківський район (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
						Київ (<span>24</span>)
					</span>
                                                    </label>
                                                </div>
                                                <div class="my-dropdown-item">
                                                    <label class="my-dropdown-item-label-checkbox">
                                                        <input class="my-dropdown-item-checkbox" type="checkbox">
                                                        <span class="my-dropdown-item-checkbox-text">
						Харків (<span>24</span>)
					</span>
                                                    </label>
                                                    <div class="my-dropdown-next-list" style="display: none">
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									АНД район (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									Індустріальний район (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									Центральний район (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									Новокадацький район (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                        <div class="my-dropdown-item">
                                                            <label class="my-dropdown-item-label-checkbox">
                                                                <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                <span class="my-dropdown-item-checkbox-text">
									Шевченківський район (<span>24</span>)
								</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="my-dropdown-search-wrapper" style="display: none">
                                        <div class="my-dropdown-search-list">
                                            <div class="scroller">
                                                <div class="my-dropdown-search-item">
                                                    <div class="eqweqw">
                                                        Одесская обл (24)
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item selects w16">
			<span>
				<label class="item-label" for="agency-branch-street">Улица</label>
			</span>
                                <select id="agency-branch-street" class="js-example-responsive2 my-select2" autocomplete="off">
                                    <option></option>
                                    <option>
                                        Тенистая
                                    </option>
                                </select>
                            </div>
                            <div class="item w16 noresize120">
			<span>
				<label class="item-label" for="current-house">№ Дом</label> /
				<label for="current-offices">офис</label>
			</span>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="current-house" type="text" autocomplete="off">
                                    /
                                    <input class="item-inputText" id="current-offices" type="text" autocomplete="off">
                                </div>
                            </div>
                            <div class="item selects w16">
			<span>
				<label class="item-label" for="agency-branch-metro">Ориентир / Станция</label>
			</span>
                                <select id="agency-branch-metro" class="js-example-responsive3 my-select2" autocomplete="off">
                                    <option></option>
                                    <option>
                                        1995
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="block-my-btns">
                <div class="block-my-btns-left">
                    <button class="btn btn-outline-primary" type="button">
                        Отменить
                    </button>
                </div>
                <div class="block-my-btns-right">
                    <button class="btn btn-primary" type="submit">
                        Добавить
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- кінець main	-->
</main>
<!-- початок цей блок ще в розробці _modal	-->
<div class="modal fade" id="add-contact-modal" tabindex="-1" aria-labelledby="geoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-body-l d-flex align-items-center mb-0 justify-content-between">
                    <h2 class="modal-title" id="exampleContactModalLabel">
                        <span>Контакт ID1234567</span>
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body-l">
                    <h3 class="modal-body-title">
                        <span>Основное</span>
                    </h3>
                    <div class="modal-row">
                        <div class="item">
                            <label for="name-contact-modal" class="green">Имя</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="name-contact-modal" type="text" autocomplete="off"
                                       placeholder="Имя">
                            </div>
                        </div>
                        <div class="item">
                            <label for="surname-contact-modal">Фамилия</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="surname-contact-modal" type="text" autocomplete="off"
                                       placeholder="Фамилия">
                            </div>
                        </div>
                        <div class="item">
                            <label for="father-name-contact-modal">Отчество</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="father-name-contact-modal" type="text" autocomplete="off"
                                       placeholder="Отчество">
                            </div>
                        </div>
                    </div>
                    <div class="modal-row">
                        <div class="item phone">
                            <div class="item" data-phone-item>
                                <div class="add_new-tel">
                                    <button type="button" class="btn btn-new-tel">
                                        <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z" fill="#3585F5" />
                                            <path d="M4.583 9.58476C4.46922 9.47098 4.39889 9.31387 4.39889 9.14032L4.39889 0.918164C4.39883 0.571001 4.68022 0.289614 5.02739 0.28967C5.37449 0.28967 5.65588 0.571056 5.65588 0.918164L5.65588 9.14032C5.65583 9.48748 5.37449 9.76881 5.02733 9.76887C4.85389 9.76887 4.69678 9.69853 4.583 9.58476Z" fill="#3585F5" />
                                        </svg>
                                    </button>
                                </div>
                                <label for="tel-contact1-modal" class="green">Телефон / Логин</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText tel-contact" id="tel-contact1-modal" type="tel" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <label for="email-contact-modal">Email</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText " id="email-contact-modal" type="email" autocomplete="off" placeholder="email@gmail.com">
                            </div>
                        </div>
                        <div class="item selects">
                            <label class="item-label green" for="type-contact-modal">Тип контакта</label>
                            <select id="type-contact-modal" class="js-example-responsive2 my-select2" autocomplete="off">
                                <option></option>
                                <option value="company1">
                                    тип1
                                </option>
                                <option value="company2">
                                    тип2
                                </option>
                                <option value="company3">
                                    тип3
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-row">
                        <div class="item w25 selects">
                            <label class="item-label green" for="tags-client-modal">Теги</label>
                            <select id="tags-client-modal" class="js-example-responsive2 my-select2" autocomplete="off">
                                <option></option>
                                <option value="company1">
                                    Посредник1
                                </option>
                                <option value="company2">
                                    Посредник2
                                </option>
                                <option value="company3">
                                    Посредник3
                                </option>
                            </select>
                        </div>
                        <div class="item w75">
                            <label for="comment-contact-modal" >Комментарий</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="comment-contact-modal" type="text" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                    <div class="modal-row files">
                        <div class="item photo-loader">
							<span class="label">
								Фото
							</span>
                            <div class="photo-info-list-wrapper">
                                <ul class="photo-info-list">
                                    <li class="photo-info-btn-wrapper">
                                        <label class="photo-info-btn" for="loading-photo-contact-modal">
                                            <input type="file" id="loading-photo-contact-modal" name="loading-photo" accept="image/png, image/jpg, image/jpeg, image/heic">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.50725 13.2938C7.814 13.9437 6.89515 14.2986 5.945 14.2833C4.99486 14.2679 4.08791 13.8837 3.41597 13.2117C2.74403 12.5398 2.35977 11.6329 2.34446 10.6827C2.32914 9.73256 2.68398 8.81371 3.33392 8.12046L9.17392 2.28713C9.52109 1.94269 9.96235 1.70858 10.4422 1.61425C10.9221 1.51991 11.4191 1.56956 11.8708 1.75695C12.3226 1.94433 12.7088 2.2611 12.981 2.6674C13.2532 3.0737 13.3992 3.55141 13.4006 4.04046C13.4002 4.36567 13.3352 4.68757 13.2093 4.98743C13.0834 5.28729 12.8992 5.55912 12.6672 5.78713L7.11392 11.3338C6.94029 11.4722 6.72193 11.5421 6.50022 11.5302C6.27851 11.5183 6.06887 11.4254 5.91105 11.2692C5.75324 11.1131 5.65821 10.9044 5.64399 10.6828C5.62977 10.4613 5.69735 10.2422 5.83392 10.0671L11.3939 4.50713L10.4472 3.56713L4.88725 9.12713C4.486 9.55082 4.26593 10.1144 4.27387 10.6978C4.2818 11.2813 4.51712 11.8387 4.92974 12.2513C5.34236 12.6639 5.89971 12.8992 6.4832 12.9072C7.06668 12.9151 7.63022 12.695 8.05392 12.2938L13.6206 6.73379C14.3367 6.01859 14.7393 5.04822 14.7399 4.03615C14.7406 3.02408 14.3391 2.05321 13.6239 1.33713C12.9087 0.621043 11.9383 0.218399 10.9263 0.217774C9.9142 0.217149 8.94333 0.618593 8.22725 1.33379L2.38725 7.18046C1.4841 8.1234 0.986222 9.38258 1.00029 10.6882C1.01436 11.9938 1.53926 13.2419 2.46251 14.1652C3.38577 15.0885 4.63393 15.6133 5.93953 15.6274C7.24513 15.6415 8.50431 15.1436 9.44725 14.2405L14.7272 8.95379L13.7872 8.00046L8.50725 13.2938Z" fill="#3585F5" />
                                            </svg>
                                            <span>
												Загрузить фото
											</span>
                                        </label>
                                    </li>
                                </ul>
                                <div class="error-container"></div>
                            </div>
                        </div>
                        <div class="item-row">
                            <div class="item w33">
								<span>
									<label class="item-label" for="telegram">Telegram</label>
								</span>
                                <input class="item-inputText" id="telegram" type="text" autocomplete="off" placeholder="@profilename">
                            </div>
                            <div class="item w33">
								<span>
									<label class="item-label" for="viber">Viber</label>
								</span>
                                <input class="item-inputText" id="viber" type="text" autocomplete="off" placeholder="@profilename">
                            </div>
                            <div class="item w33">
								<span>
									<label class="item-label" for="whatsapp">Whatsapp</label>
								</span>
                                <input class="item-inputText" id="whatsapp" type="text" autocomplete="off" placeholder="@profilename">
                            </div>
                            <div class="item w33">
								<span>
									<label class="item-label" for="tiktok">TikTok</label>
								</span>
                                <input class="item-inputText" id="tiktok" type="text" autocomplete="off" placeholder="@profilename">
                            </div>
                            <div class="item w33">
								<span>
									<label class="item-label" for="instagram">Instagram</label>
								</span>
                                <input class="item-inputText" id="instagram" type="text" autocomplete="off" placeholder="@profilename">
                            </div>
                            <div class="item w33">
								<span>
									<label class="item-label" for="facebook">Facebook</label>
								</span>
                                <input class="item-inputText" id="facebook" type="text" autocomplete="off" placeholder="http://www.facebook.com/profilename">
                            </div>
                            <div class="item w50">
                                <label for="passport-contact-modal" >Паспорт</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="passport-contact-modal" type="text" autocomplete="off" placeholder="АА123456">
                                </div>
                            </div>
                            <div class="item w25">
                                <label for="inn-contact-modal" >ИНН</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="inn-contact-modal" type="text" autocomplete="off" placeholder="1234567890">
                                </div>
                            </div>
                            <div class="item w25 data">
								<span>
									<label for="datapiker-contact-modal">День рождения</label>
								</span>
                                <span>
									<input class="item-inputText date-piker" type="text" id="datapiker-contact-modal" autocomplete="off">
									<picture><source srcset="./img/icon/calendar.svg" type="image/webp"><img src="./img/icon/calendar.svg" alt=""></picture>
								</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-row">
                        <button class="modal-body-title btn" type="button"  data-bs-toggle="collapse" data-bs-target="#collapseContactExample" aria-expanded="false" aria-controls="collapseContactExample">
                            <span>История изменений</span>
                        </button>
                        <div class="collapse" id="collapseContactExample">
                            <ul class="history-info">
                                <li class="history-info-item">
                                    <span>15:40 20.12.2024 — агент ID1234567</span>
                                    <p>Изменен номер телефона с +380 (12) 345-67-89 на +380 (12) 345-67-89</p>
                                </li>
                                <li class="history-info-item">
                                    <span>15:40 20.12.2024 — агент ID1234567</span>
                                    <p>Какие-то изменения данных</p>
                                </li>
                                <li class="history-info-item">
                                    <span>15:40 20.12.2024 — агент ID1234567</span>
                                    <p>Вариант изменения данных</p>
                                </li>
                                <li class="history-info-item">
                                    <span>15:40 20.12.2024 — агент ID1234567</span>
                                    <p>Вариант изменения данных</p>
                                </li>
                                <li class="history-info-item">
                                    <span>15:40 20.12.2024 — агент ID1234567</span>
                                    <p>Изменен номер телефона с +380 (12) 345-67-89 на +380 (12) 345-67-89</p>
                                </li>
                                <li class="history-info-item">
                                    <span>15:40 20.12.2024 — агент ID1234567</span>
                                    <p>Изменен номер телефона с +380 (12) 345-67-89 на +380 (12) 345-67-89</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-body-l mb-0">
                    <button class="btn btn-outline-primary" type="button">
                        Отменить
                    </button>
                    <button class="btn btn-primary" type="button">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="geoModal" tabindex="-1" aria-labelledby="geoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-body-l d-flex align-items-center justify-content-between">
                    <h2 class="modal-title" id="exampleGeoModalLabel">
                        <span>Локация</span>
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body-l">
                    <label for="address">Адрес</label>
                    <input type="text" id="address" class="form-control">
                </div>
                <div class="modal-body-l">
                    <div class="row">
                        <div class="col-12">
                            <span class="label">Координаты</span>
                        </div>
                        <div class="col-6">
                            <label class="d-block">
                                <input type="text" id="latitude" class="form-control" placeholder="Широта" readonly>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="d-block">
                                <input type="text" id="longitude" class="form-control" placeholder="Долгота" readonly>
                            </label>
                        </div>
                    </div>
                </div>
                <div id="map-container" style="height: 400px; width: 100%; margin-top: 15px;"></div>
                <div class="modal-body-l mt-3">
                    <button class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- кінець цей блок ще в розробці _modal	-->
<script src="./js/lib/popper.v2.11.8.min.js"></script>
<script src="./js/lib/bootstrap.v5.3.3.min.js"></script>
<script src="./js/lib/jquery.v3.7.1.min.js"></script>
<script src="./js/lib/select2.min.js"></script>
<script src="./js/lib/fancybox.min.js"></script>
<script src="./js/lib/moment.min.js"></script>
<script src="./js/lib/data-range-picker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<!-- для роботи Гео Модалки 27.08.2025 -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-text-icon@1.0.0/dist/leaflet.text-icon.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script src="./js/pages/modal-geo.min.js"></script>
<!-- для роботи Гео Модалки 27.08.2025 -->
<script src="./js/lib/heic2any.min.js"></script>
<script src="./js/pages/function_on_pages-create.min.js" type="module"></script>
<script src="./js/pages/my-dropdown.min.js"></script>
<script src="./js/pages/page-create-agency.js" type="module"></script>
<script src="./js/pages/add-contact-modal.min.js" type="module"></script>
</body>
</html>
