<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Page-Company-Company</title>
    <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/lib/select2.min.css">
    <link rel="stylesheet" href="./css/lib/bootstrap.v5.3.3.min.css">
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <!-- для роботи Гео Модалки 27.08.2025 -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
    <!-- для роботи Гео Модалки 27.08.2025 -->
    <link rel="stylesheet" href="./css/pages/page-company-company.css">
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
        <!-- початок header	-->
        <header class="header-wrapper">
            <h1 class="header-title">
                Компания
            </h1>
            <div class="header-tabs">
                <div class="btn-group">
                    <a href="./page-company-agents.html" class="btn btn-outline-primary active" aria-current="page">Команда</a>
                    <a href="./page-company-offices.html" class="btn btn-outline-primary">Офисы</a>
                    <a href="./page-company-company.html" class="btn btn-outline-primary">Компании</a>
                </div>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#add-employee-modal">
				<span>
					Добавить
				</span>
                    <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z" fill="white" />
                        <path d="M4.583 9.58476C4.46922 9.47098 4.39889 9.31387 4.39889 9.14032L4.39889 0.918164C4.39883 0.571001 4.68022 0.289614 5.02739 0.28967C5.37449 0.28967 5.65588 0.571056 5.65588 0.918164L5.65588 9.14032C5.65583 9.48748 5.37449 9.76881 5.02733 9.76887C4.85389 9.76887 4.69678 9.69853 4.583 9.58476Z" fill="white" />
                    </svg>
                </button>
            </div>
        </header>

        <!-- кінець header	-->
        <!-- початок filter	-->
        <div class="filter">
            <div class="filter-header">
                <label for="company">
                    <select id="company" class="js-example-responsive2 company">
                        <option></option>
                        <option>Дома</option>
                        <option>Участки</option>
                        <option>Коммерческая</option>
                    </select>
                </label>
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
                <label for="search-name-email-phone" class="input-search">
                    <input id="search-name-email-phone" autocomplete="off" type="text" placeholder="Поиск по названию">
                </label>
                <label for="companyStatusFilter">
                    <select id="companyStatusFilter" class="js-example-responsive2 currency">
                        <option selected>Все</option>
                        <option>Активные</option>
                        <option>Архив</option>
                    </select>
                </label>
                <div class="header-btn">
                    <div class="full-filter-btn-wrapper">
                        <button class="btn btn-primary" disabled id="full-filter-btn">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9 6.2C8.08562 6.2 7.30774 5.61561 7.01947 4.79994L2.7 4.8C2.3134 4.8 2 4.4866 2 4.1C2 3.7134 2.3134 3.4 2.7 3.4L7.01972 3.39937C7.30818 2.58406 8.08588 2 9 2C9.91412 2 10.6918 2.58406 10.9803 3.39937L15.3 3.4C15.6866 3.4 16 3.7134 16 4.1C16 4.4866 15.6866 4.8 15.3 4.8L10.9805 4.79994C10.6923 5.61561 9.91438 6.2 9 6.2ZM9 4.8C9.3866 4.8 9.7 4.4866 9.7 4.1C9.7 3.7134 9.3866 3.4 9 3.4C8.6134 3.4 8.3 3.7134 8.3 4.1C8.3 4.4866 8.6134 4.8 9 4.8ZM4.1 11.1C2.9402 11.1 2 10.1598 2 9C2 7.8402 2.9402 6.9 4.1 6.9C5.01412 6.9 5.79182 7.48406 6.08028 8.29937L15.3 8.3C15.6866 8.3 16 8.6134 16 9C16 9.3866 15.6866 9.7 15.3 9.7L6.08053 9.69994C5.79226 10.5156 5.01438 11.1 4.1 11.1ZM4.1 9.7C4.4866 9.7 4.8 9.3866 4.8 9C4.8 8.6134 4.4866 8.3 4.1 8.3C3.7134 8.3 3.4 8.6134 3.4 9C3.4 9.3866 3.7134 9.7 4.1 9.7ZM13.9 16C12.9817 16 12.2011 15.4106 11.9158 14.5895C11.8784 14.5967 11.8396 14.6 11.8 14.6H2.7C2.3134 14.6 2 14.2866 2 13.9C2 13.5134 2.3134 13.2 2.7 13.2H11.8C11.8396 13.2 11.8784 13.2033 11.9162 13.2096C12.2011 12.3894 12.9817 11.8 13.9 11.8C15.0598 11.8 16 12.7402 16 13.9C16 15.0598 15.0598 16 13.9 16ZM13.9 14.6C14.2866 14.6 14.6 14.2866 14.6 13.9C14.6 13.5134 14.2866 13.2 13.9 13.2C13.5134 13.2 13.2 13.5134 13.2 13.9C13.2 14.2866 13.5134 14.6 13.9 14.6Z" fill="#3585F5"/>
                            </svg>
                        </button>
                        <!--				<div class="full-filter-counter">-->
                        <!--					<span>20</span>-->
                        <!--					<button type="button" id="delete-params-on-filter">-->
                        <!--						<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">-->
                        <!--							<path d="M0.878207 9.87891C0.653517 9.87891 0.428717 9.79318 0.257263 9.62173C-0.0857544 9.27871 -0.0857544 8.72285 0.257263 8.37984L8.37992 0.257181C8.72294 -0.085727 9.27879 -0.085727 9.62181 0.257181C9.96472 0.600089 9.96472 1.15605 9.62181 1.49896L1.49915 9.62162C1.3277 9.79318 1.1029 9.87891 0.878207 9.87891Z" fill="#AAAAAA"/>-->
                        <!--							<path d="M9.00086 9.8788C8.77606 9.8788 8.55137 9.79307 8.37992 9.62162L0.257263 1.49896C-0.0857544 1.15605 -0.0857544 0.600089 0.257263 0.257181C0.600171 -0.085727 1.15613 -0.085727 1.49904 0.257181L9.6217 8.37984C9.96461 8.72285 9.96461 9.27871 9.6217 9.62173C9.45035 9.79307 9.22566 9.8788 9.00086 9.8788Z" fill="#AAAAAA"/>-->
                        <!--						</svg>-->
                        <!--					</button>-->
                        <!--				</div>-->
                    </div>
                </div>
            </div>
        </div>
        <!-- кінець filter	-->
        <!-- початок filter	-->
        <div>
            <!-- початок table	-->
            <table id="example" class="display" style="width:100%">
                <col width="3.0%" valign="middle">
                <col width="6.0%" valign="middle">
                <col width="20.038%" valign="middle">
                <col width="18.587%" valign="middle">
                <col width="5.587%" valign="middle">
                <col width="6.0%" valign="middle">
                <col width="6.0%" valign="middle">
                <col width="6.0%" valign="middle">
                <col width="8.0%" valign="middle">
                <col width="9.223%" valign="middle">
                <col width="11.565%" valign="middle">
                <thead>
                <tr>
                    <th>
                        <div class="thead-wrapper checkBox">
                            <label class="my-custom-input">
                                <input type="checkbox">
                                <span class="my-custom-box"></span>
                            </label>
                        </div>
                    </th>
                    <th>
                        <div class="thead-wrapper photo">
                            <p>Фото</p>
                        </div>
                    </th>
                    <th>
                        <div class="thead-wrapper company">
                            <p>Компания</p>
                        </div>
                    </th>
                    <th>
                        <div class="thead-wrapper responsible">
                            <p>Ответственный</p>
                        </div>
                    </th>
                    <th>
                        <div class="thead-wrapper offices">
                            <p>Офисы</p>
                        </div>
                    </th>
                    <th>
                        <div class="thead-wrapper command">
                            <p>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                                </svg>
                                <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Команда">
							<picture><source srcset="./img/icon/icon-info.svg" type="image/webp"><img src="./img/icon/icon-info.svg" alt=""></picture>
						</span>
                            </p>
                        </div>
                    </th>
                    <th>
                        <div class="thead-wrapper object">
                            <p>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16">
                                    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                                    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                                </svg>
                                <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Объекты">
							<picture><source srcset="./img/icon/icon-info.svg" type="image/webp"><img src="./img/icon/icon-info.svg" alt=""></picture>
						</span>
                            </p>
                        </div>
                    </th>



                    <th>
                        <div class="thead-wrapper commission">
                            <p>Комиссия</p>
                        </div>
                    </th>
                    <th>
                        <div class="thead-wrapper block-actions">
                            <div class="menu-burger">
                                <div class="dropdown">
                                    <button class="btn " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <picture><source srcset="./img/icon/burger.svg" type="image/webp"><img src="./img/icon/burger.svg" alt=""></picture>
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
                            <div class="menu-burger">
                                <div class="dropdown">
                                    <button class="btn " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <picture><source srcset="./img/icon/sorting.svg" type="image/webp"><img src="./img/icon/sorting.svg" alt=""></picture>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Самые новые</a></li>
                                        <li><a class="dropdown-item" href="#">Самые дешевые</a></li>
                                        <li><a class="dropdown-item" href="#">Самые дорогие</a></li>
                                        <li><a class="dropdown-item" href="#">Самые дешевые/м<sup>2</sup></a></li>
                                        <li><a class="dropdown-item" href="#">Самые дорогие/м<sup>2</sup></a></li>
                                        <li><a class="dropdown-item" href="#">Наименьшая площадь</a></li>
                                        <li><a class="dropdown-item" href="#">Наибольшая площадь</a></li>
                                        <li><a class="dropdown-item" href="#">Самые старые</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <div class="tbody-wrapper checkBox">
                            <label class="my-custom-input">
                                <input type="checkbox">
                                <span class="my-custom-box"></span>
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper photo">
                            <div class="developer-wrapper">
                                <div>
                                    <picture><source srcset="./img/complex2.webp" type="image/webp"><img src="./img/complex2.png" alt=""></picture>
                                </div>
                            </div>
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
                            <div>
                                <p class="link-name" data-hover-agent>
                                    Федотов Василий
                                </p>
                                <span>Менеджер</span>
                                <a href="tel:380968796542">+380968796542</a>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper offices">
                            <p><button class="info-footer-btn btn-others" type="button">10</button></p>
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
                        <div class="tbody-wrapper commission">
                            <p>от 1000</p>
                            <span>до 1000000</span>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper block-actions">
                            <a href="#" class="btn mail-link" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="top"
                               data-bs-title="Написать">
                                <picture><source srcset="./img/icon/mail.svg" type="image/webp"><img src="./img/icon/mail.svg" alt=""></picture>
                            </a>
                            <div class="block-actions-wrapper">
                                <div class="menu-burger">
                                    <div class="dropdown">
                                        <button class="btn " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <picture><source srcset="./img/icon/burger-blue.svg" type="image/webp"><img src="./img/icon/burger-blue.svg" alt=""></picture>
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
								<picture><source srcset="./img/icon/bookmark.svg" type="image/webp"><img class="non-checked" src="./img/icon/bookmark.svg" alt=""></picture>
								<picture><source srcset="./img/icon/bookmark-cheked.svg" type="image/webp"><img class="on-checked" src="./img/icon/bookmark-cheked.svg" alt=""></picture>
							</span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <!-- кінець table	-->
        </div>
        <!-- кінець filter	-->
    </div>
    <!-- кінець main	-->
</main>
<!-- початок цей блок ще в розробці _modal	-->
<div class="modal fade" id="geoModal" tabindex="-1" aria-labelledby="geoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-body-l d-flex align-items-center justify-content-between">
                    <h4 class="modal-title" id="exampleModalLabel">
                        <span>Локация</span>
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body-l">
                    <label for="address">Адрес</label>
                    <input type="text" id="address" class="form-control">
                    <div id="addressStatus" class="address-status">Оберіть місце на карті</div>
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
                                <input type="text" id="longitude" class="form-control" placeholder="Долгота"
                                       readonly>
                            </label>
                        </div>
                    </div>
                </div>
                <div id="map-container"></div>
                <div class="modal-body-l mt-3">
                    <button id="saveLocation" class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- кінець цей блок ще в розробці _modal	-->
<script src="./js/lib/popper.v2.11.8.min.js"></script>
<script src="./js/lib/bootstrap.v5.3.3.min.js"></script>
<script src="./js/lib/jquery.v3.7.1.min.js"></script>
<script src="./js/lib/data-tables.min.js"></script>
<script src="./js/lib/select2.min.js"></script>
<!-- для роботи Гео Модалки 27.08.2025 -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-text-icon@1.0.0/dist/leaflet.text-icon.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script src="./js/pages/modal-geo.min.js"></script>
<!-- для роботи Гео Модалки 27.08.2025 -->
<script src="./js/pages/info-agent-or-contact-modal.min.js" type="module"></script>

<script src="./js/pages/companies/index/page-company-company.js" type="module"></script>

<script src="./js/pages/filter2.min.js"></script>
<script src="./js/pages/my-dropdown.min.js"></script>

</body>
</html>
