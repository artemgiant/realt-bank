<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Page-Complex</title>
    <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/lib/select2.min.css">
    <link rel="stylesheet" href="./css/lib/bootstrap.v5.3.3.min.css">
    <link rel="stylesheet" href="./css/lib/data-range-picker.min.css">
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <!-- для роботи Гео Модалки 27.08.2025 -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
    <!-- для роботи Гео Модалки 27.08.2025 -->
    <link rel="stylesheet" href="./css/pages/page-complex.css">
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
                Недвижимость
            </h1>
            <div class="header-tabs">
                <div class="btn-group">
                    <a href="./page-home.html" class="btn btn-outline-primary active" aria-current="page">Объекты</a>
                    <a href="./page-complex.html" class="btn btn-outline-primary">Комплексы</a>
                    <a href="./page-developers.html" class="btn btn-outline-primary">Девелоперы</a>
                </div>
            </div>
            <div class="header-actions">
                <a class="btn btn-primary" href="#">
			<span>
				Добавить
			</span>
                    <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z" fill="white" />
                        <path d="M4.583 9.58476C4.46922 9.47098 4.39889 9.31387 4.39889 9.14032L4.39889 0.918164C4.39883 0.571001 4.68022 0.289614 5.02739 0.28967C5.37449 0.28967 5.65588 0.571056 5.65588 0.918164L5.65588 9.14032C5.65583 9.48748 5.37449 9.76881 5.02733 9.76887C4.85389 9.76887 4.69678 9.69853 4.583 9.58476Z" fill="white" />
                    </svg>
                </a>
            </div>
        </header>

        <!-- кінець header	-->
        <!-- початок filter	-->
        <div class="filter">
            <div class="filter-header">
                <!--			оновлений код-->
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
                <!--			оновлений код кінець-->
                <label for="type" class="blue-select2">
                    <select id="type" class="js-example-responsive2 ">
                        <option selected value="apartments-for-sale">
                            Квартиры
                        </option>
                        <option value="houses-for-sale">
                            Дома
                        </option>
                        <option value="land-sale">
                            Участки
                        </option>
                        <option value="sale-of-commerce">
                            Коммерческая
                        </option>
                    </select>
                </label>
                <div class="header-price">
                    <label class="" for="price-from">
                        <input class="form-control" type="text" id="price-from" autocomplete="off" placeholder="Цена от">
                    </label>
                    <label for="price-to">
                        <input class="form-control" type="text" id="price-to" autocomplete="off" placeholder="Цена до">
                    </label>
                    <label for="price">
                        <select id="price" class="js-example-responsive3">
                            <option value="USD" selected>
                                USD
                            </option>
                            <option value="UAH">
                                UAH
                            </option>
                            <option value="EUR">
                                EUR
                            </option>
                        </select>
                    </label>
                </div>
                <div class="header-btn">
                    <!--			23.04 перероблений елент нижче-->
                    <div class="full-filter-btn-wrapper">
                        <button class="btn btn-primary" id="full-filter-btn">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9 6.2C8.08562 6.2 7.30774 5.61561 7.01947 4.79994L2.7 4.8C2.3134 4.8 2 4.4866 2 4.1C2 3.7134 2.3134 3.4 2.7 3.4L7.01972 3.39937C7.30818 2.58406 8.08588 2 9 2C9.91412 2 10.6918 2.58406 10.9803 3.39937L15.3 3.4C15.6866 3.4 16 3.7134 16 4.1C16 4.4866 15.6866 4.8 15.3 4.8L10.9805 4.79994C10.6923 5.61561 9.91438 6.2 9 6.2ZM9 4.8C9.3866 4.8 9.7 4.4866 9.7 4.1C9.7 3.7134 9.3866 3.4 9 3.4C8.6134 3.4 8.3 3.7134 8.3 4.1C8.3 4.4866 8.6134 4.8 9 4.8ZM4.1 11.1C2.9402 11.1 2 10.1598 2 9C2 7.8402 2.9402 6.9 4.1 6.9C5.01412 6.9 5.79182 7.48406 6.08028 8.29937L15.3 8.3C15.6866 8.3 16 8.6134 16 9C16 9.3866 15.6866 9.7 15.3 9.7L6.08053 9.69994C5.79226 10.5156 5.01438 11.1 4.1 11.1ZM4.1 9.7C4.4866 9.7 4.8 9.3866 4.8 9C4.8 8.6134 4.4866 8.3 4.1 8.3C3.7134 8.3 3.4 8.6134 3.4 9C3.4 9.3866 3.7134 9.7 4.1 9.7ZM13.9 16C12.9817 16 12.2011 15.4106 11.9158 14.5895C11.8784 14.5967 11.8396 14.6 11.8 14.6H2.7C2.3134 14.6 2 14.2866 2 13.9C2 13.5134 2.3134 13.2 2.7 13.2H11.8C11.8396 13.2 11.8784 13.2033 11.9162 13.2096C12.2011 12.3894 12.9817 11.8 13.9 11.8C15.0598 11.8 16 12.7402 16 13.9C16 15.0598 15.0598 16 13.9 16ZM13.9 14.6C14.2866 14.6 14.6 14.2866 14.6 13.9C14.6 13.5134 14.2866 13.2 13.9 13.2C13.5134 13.2 13.2 13.5134 13.2 13.9C13.2 14.2866 13.5134 14.6 13.9 14.6Z" fill="#3585F5"/>
                            </svg>
                        </button>
                        <div class="full-filter-counter">
                            <span>20</span>
                            <button type="button" id="delete-params-on-filter">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.878207 9.87891C0.653517 9.87891 0.428717 9.79318 0.257263 9.62173C-0.0857544 9.27871 -0.0857544 8.72285 0.257263 8.37984L8.37992 0.257181C8.72294 -0.085727 9.27879 -0.085727 9.62181 0.257181C9.96472 0.600089 9.96472 1.15605 9.62181 1.49896L1.49915 9.62162C1.3277 9.79318 1.1029 9.87891 0.878207 9.87891Z" fill="#AAAAAA"/>
                                    <path d="M9.00086 9.8788C8.77606 9.8788 8.55137 9.79307 8.37992 9.62162L0.257263 1.49896C-0.0857544 1.15605 -0.0857544 0.600089 0.257263 0.257181C0.600171 -0.085727 1.15613 -0.085727 1.49904 0.257181L9.6217 8.37984C9.96461 8.72285 9.96461 9.27871 9.6217 9.62173C9.45035 9.79307 9.22566 9.8788 9.00086 9.8788Z" fill="#AAAAAA"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="filter-tags">
                <div class="badge rounded-pill qwe1">
                    Параметр из фильтра
                    <button type="button" aria-label="Close">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.878207 9.87891C0.653517 9.87891 0.428717 9.79318 0.257263 9.62173C-0.0857544 9.27871 -0.0857544 8.72285 0.257263 8.37984L8.37992 0.257181C8.72294 -0.085727 9.27879 -0.085727 9.62181 0.257181C9.96472 0.600089 9.96472 1.15605 9.62181 1.49896L1.49915 9.62162C1.3277 9.79318 1.1029 9.87891 0.878207 9.87891Z" fill="#AAAAAA"/>
                            <path d="M9.00086 9.8788C8.77606 9.8788 8.55137 9.79307 8.37992 9.62162L0.257263 1.49896C-0.0857544 1.15605 -0.0857544 0.600089 0.257263 0.257181C0.600171 -0.085727 1.15613 -0.085727 1.49904 0.257181L9.6217 8.37984C9.96461 8.72285 9.96461 9.27871 9.6217 9.62173C9.45035 9.79307 9.22566 9.8788 9.00086 9.8788Z" fill="#AAAAAA"/>
                        </svg>
                    </button>
                </div>
                <div class="badge rounded-pill qwe2">
                    Параметр из фильтра
                    <button type="button" aria-label="Close">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.878207 9.87891C0.653517 9.87891 0.428717 9.79318 0.257263 9.62173C-0.0857544 9.27871 -0.0857544 8.72285 0.257263 8.37984L8.37992 0.257181C8.72294 -0.085727 9.27879 -0.085727 9.62181 0.257181C9.96472 0.600089 9.96472 1.15605 9.62181 1.49896L1.49915 9.62162C1.3277 9.79318 1.1029 9.87891 0.878207 9.87891Z" fill="#AAAAAA"/>
                            <path d="M9.00086 9.8788C8.77606 9.8788 8.55137 9.79307 8.37992 9.62162L0.257263 1.49896C-0.0857544 1.15605 -0.0857544 0.600089 0.257263 0.257181C0.600171 -0.085727 1.15613 -0.085727 1.49904 0.257181L9.6217 8.37984C9.96461 8.72285 9.96461 9.27871 9.6217 9.62173C9.45035 9.79307 9.22566 9.8788 9.00086 9.8788Z" fill="#AAAAAA"/>
                        </svg>
                    </button>
                </div>
                <div class="badge rounded-pill qwe2">
                    Параметр из фильтра
                    <button type="button" aria-label="Close">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.878207 9.87891C0.653517 9.87891 0.428717 9.79318 0.257263 9.62173C-0.0857544 9.27871 -0.0857544 8.72285 0.257263 8.37984L8.37992 0.257181C8.72294 -0.085727 9.27879 -0.085727 9.62181 0.257181C9.96472 0.600089 9.96472 1.15605 9.62181 1.49896L1.49915 9.62162C1.3277 9.79318 1.1029 9.87891 0.878207 9.87891Z" fill="#AAAAAA"/>
                            <path d="M9.00086 9.8788C8.77606 9.8788 8.55137 9.79307 8.37992 9.62162L0.257263 1.49896C-0.0857544 1.15605 -0.0857544 0.600089 0.257263 0.257181C0.600171 -0.085727 1.15613 -0.085727 1.49904 0.257181L9.6217 8.37984C9.96461 8.72285 9.96461 9.27871 9.6217 9.62173C9.45035 9.79307 9.22566 9.8788 9.00086 9.8788Z" fill="#AAAAAA"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="full-filter">
                <h3 class="full-filter-title">
                    <span>Расширенный фильтр</span>
                </h3>
                <div class="full-filter-wrapper">
                    <div class="full-filter-row">
                        <div class="item">
                            <label class="item-label" for="full-filter-developer">Девелопер</label>
                            <select id="full-filter-developer" class="js-example-responsive2" autocomplete="off">
                                <option></option>
                                <option>Все Девелопер</option>
                                <option>Девелопер1</option>
                                <option>Девелопер2</option>
                            </select>
                        </div>
                        <div class="item">
                            <span class="item-label">Комплекс</span>
                            <div class="multiple-menu">
                                <button class="multiple-menu-btn" data-open-menu="false">
                                    Все
                                </button>
                                <div class="multiple-menu-wrapper">
                                    <label>
                                        <input class="multiple-menu-search" autocomplete="off" name="complex-search" type="text" placeholder="Поиск">
                                    </label>
                                    <ul class="multiple-menu-list">
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input data-name="checkbox-all" type="checkbox" name="complex-all" checked>
                                                <span></span>
                                                Все
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="complex_1">
                                                <span></span>
                                                complex 1
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="complex_2">
                                                <span></span>
                                                complex 2
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="complex_3">
                                                <span></span>
                                                complex 3
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <span class="item-label">Улица</span>
                            <div class="multiple-menu">
                                <button class="multiple-menu-btn" data-open-menu="false">
                                    Все
                                </button>
                                <div class="multiple-menu-wrapper">
                                    <label>
                                        <input class="multiple-menu-search" autocomplete="off" name="street-search" type="text" placeholder="Поиск">
                                    </label>
                                    <ul class="multiple-menu-list">
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input data-name="checkbox-all" type="checkbox" name="street-all" checked>
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">Все</span>
                                            </label>
                                        </li>
                                        <!-- 30.07.2025 -->
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="street_1">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">улица 1</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="street_2">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">Улица 2</span>
                                            </label>
                                        </li>
                                        <!-- 30.07.2025 -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <span class="item-label">Ориентир/Станция</span>
                            <div class="multiple-menu">
                                <button class="multiple-menu-btn" data-open-menu="false">
                                    Все
                                </button>
                                <div class="multiple-menu-wrapper">
                                    <label>
                                        <input class="multiple-menu-search" autocomplete="off" name="metro-search" type="text" placeholder="Поиск">
                                    </label>
                                    <ul class="multiple-menu-list">
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input data-name="checkbox-all" type="checkbox" name="metro-all" checked>
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">Все</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="metro1">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">Улица 2</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="metro2">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">Улица 2</span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="full-filter-row">
                        <div class="item">
                            <span class="item-label">Тип объекта</span>
                            <div class="multiple-menu">
                                <button class="multiple-menu-btn" data-open-menu="false">
                                    Все
                                </button>
                                <div class="multiple-menu-wrapper">
                                    <ul class="multiple-menu-list">
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input data-name="checkbox-all" type="checkbox" name="wall-type-all" checked>
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">Все</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="object1">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">1</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="object2">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">2</span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <span class="item-label">Год сдачи</span>
                            <div class="multiple-menu">
                                <button class="multiple-menu-btn" data-open-menu="false">
                                    Все
                                </button>
                                <div class="multiple-menu-wrapper">
                                    <ul class="multiple-menu-list">
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input data-name="checkbox-all" type="checkbox" name="years-all" checked>
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">Все</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="years-2030">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">3123</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="years-2029">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">321</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="years-2028">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">3121</span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <span class="item-label">Состояние</span>
                            <div class="multiple-menu">
                                <button class="multiple-menu-btn" data-open-menu="false">
                                    Все
                                </button>
                                <div class="multiple-menu-wrapper">
                                    <ul class="multiple-menu-list">
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input data-name="checkbox-all" type="checkbox" name="new-buildings-all" checked>
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">312</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="new-buildings">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">32131</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="khrushchevka">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">312</span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <span class="item-label">Тип стен</span>
                            <div class="multiple-menu">
                                <button class="multiple-menu-btn" data-open-menu="false">
                                    Все
                                </button>
                                <div class="multiple-menu-wrapper">
                                    <ul class="multiple-menu-list">
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input data-name="checkbox-all" type="checkbox" name="wall-type-all" checked>
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">Все</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="brick">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">1</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="panel">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">2</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="foam-block">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">3</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="monolith">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">4</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="shellfish">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">5</span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <label class="item-label" for="number-of-floors-full-filter">Этажность</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="number-of-floors-full-filter" type="text" placeholder="От" autocomplete="off">
                                <input class="item-inputText" type="text" placeholder="До" autocomplete="off">
                            </div>
                        </div>
                        <div class="item">
                            <span class="item-label">Отопление</span>
                            <div class="multiple-menu">
                                <button class="multiple-menu-btn" data-open-menu="false">
                                    Все
                                </button>
                                <div class="multiple-menu-wrapper">
                                    <ul class="multiple-menu-list">
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input data-name="checkbox-all" type="checkbox" name="all-heating" checked>
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">Все</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="central-heating">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">1</span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="full-filter-row">
                        <div class="item search-on-id">
                            <label for="full-filter-search">Поиск по ID</label>
                            <div class="item-inputSearch-wrapper">
                                <input class="item-inputSearch" type="text" autocomplete="off" id="full-filter-search" placeholder="Поиск">
                                <button class="item-inputSearchBtn" type="button">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.1171 16C15.0002 16.0003 14.8845 15.9774 14.7767 15.9327C14.6687 15.888 14.5707 15.8223 14.4884 15.7396L11.465 12.7218C10.224 13.6956 8.6916 14.224 7.11391 14.2222C5.70692 14.2222 4.33151 13.8052 3.16164 13.0238C1.99176 12.2424 1.07995 11.1318 0.541519 9.83244C0.00308508 8.53306 -0.137797 7.1032 0.136693 5.7238C0.411184 4.34438 1.08872 3.07731 2.08362 2.0828C3.07852 1.08829 4.34609 0.411022 5.72606 0.136639C7.106 -0.137743 8.53643 0.00308386 9.83632 0.541306C11.1362 1.07953 12.2472 1.99098 13.029 3.16039C13.8106 4.3298 14.2278 5.70467 14.2278 7.11111C14.231 8.69031 13.7023 10.2245 12.7268 11.4667L15.7458 14.4889C15.8679 14.6135 15.9508 14.7714 15.9839 14.9427C16.017 15.114 15.9988 15.2914 15.9318 15.4524C15.8647 15.6136 15.7517 15.7515 15.6069 15.8488C15.462 15.9462 15.2916 15.9988 15.1171 16ZM7.11391 1.77778C6.05867 1.77778 5.02712 2.09058 4.14971 2.67661C3.2723 3.26264 2.58844 4.0956 2.18462 5.07013C1.78079 6.04467 1.67513 7.11706 1.881 8.15155C2.08687 9.18613 2.59502 10.1364 3.34119 10.8823C4.08737 11.6283 5.03806 12.1362 6.07302 12.342C7.10796 12.5477 8.18073 12.4421 9.1557 12.0385C10.1307 11.6348 10.9639 10.9512 11.5502 10.0741C12.1364 9.19706 12.4493 8.16595 12.4493 7.11111C12.4477 5.69713 11.885 4.34154 10.8848 3.3417C9.88461 2.34186 8.52843 1.77943 7.11391 1.77778Z" fill="black" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="item">
                            <label class="item-label" for="price-on-m2-filter">Цена за м²</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="price-on-m2-filter" type="text" placeholder="От" autocomplete="off">
                                <input class="item-inputText" type="text" placeholder="До" autocomplete="off">
                            </div>
                        </div>
                        <div class="item w12-5">
                            <label class="item-label" for="full-filter-currency">Валюта</label>
                            <select id="full-filter-currency" class="js-example-responsive2" autocomplete="off">
                                <option value="USD" selected>
                                    USD
                                </option>
                                <option value="UAH">
                                    UAH
                                </option>
                                <option value="EUR">
                                    EUR
                                </option>
                            </select>
                        </div>
                        <div class="item w12-5">
                            <label class="item-label" for="full-filter-level-apartments">Класс недвижимости</label>
                            <select id="full-filter-level-apartments" class="js-example-responsive2" autocomplete="off">
                                <option selected>
                                    1
                                </option>
                                <option >
                                    2
                                </option>
                                <option >
                                    3
                                </option>
                            </select>
                        </div>
                        <div class="item">
                            <span class="item-label">Особенности</span>
                            <div class="multiple-menu">
                                <button class="multiple-menu-btn" data-open-menu="false">
                                    Все
                                </button>
                                <div class="multiple-menu-wrapper">
                                    <label>
                                        <input class="multiple-menu-search" autocomplete="off" name="search-additionally" type="text" placeholder="Поиск">
                                    </label>
                                    <ul class="multiple-menu-list">
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input data-name="checkbox-all" type="checkbox" name="from-the-intermediary-all" checked>
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">Все</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="from-the-intermediary">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">1</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="state-programs">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">2</span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="item filter-btn-outline">
                            <button class="btn btn-outline-primary" type="reset">Сбросить</button>
                        </div>
                        <div class="item filter-btn">
                            <button class="btn btn-primary" type="submit">Применить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- кінець filter	-->
        <div>
            <!-- початок table	-->
            <table id="example" class="display" style="width:100%">
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
                        <div class="thead-wrapper location">
                            <p>Локация</p>
                        </div>
                    </th>
                    <th>
                        <div class="thead-wrapper type">
                            <p>Тип</p>
                        </div>
                    </th>
                    <th>
                        <div class="thead-wrapper area">
                            <p>Площадь</p>
                        </div>
                    </th>
                    <th>
                        <div class="thead-wrapper condition">
                            <p>Состояние</p>
                        </div>
                    </th>
                    <th>
                        <div class="thead-wrapper floor">
                            <p>этажность</p>
                        </div>
                    </th>
                    <th>
                        <div class="thead-wrapper photo">
                            <p>Фото</p>
                        </div>
                    </th>
                    <th>
                        <div class="thead-wrapper price">
                            <p>Цена от</p>
                        </div>
                    </th>
                    <th>
                        <div class="thead-wrapper contact">
                            <p>Контакт</p>
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
                        <div class="tbody-wrapper location">
                            <p><strong>ЖК «Южная Пальмира» </strong><span>2021-2024 г.</span></p>
                            <p>Генуэзская 5</p>
                            <span>Аркадия, Одесса, Одесский длинный</span>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper type">
                            <p>студии,</p>
                            <span>1 к, 2 к, 3 к, 4 к, 5</span>
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
                            <p>23-25</p>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper photo">
                            <picture><source srcset="./img/icon/default-foto.svg" type="image/webp"><img src="./img/icon/default-foto.svg" alt=""></picture>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper price">
                            <p>10 000 000</p>
                            <span>850/м <sup>2</sup></span>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper contact">
                            <div>
                                <p class="link-name" data-hover-contact>
                                    Федотов Василий
                                </p>
                                <p data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Гефест девелопер">
                                    Гефест девелопер
                                </p>
                                <a href="tel:380968796542">+380968796542</a>
                            </div>
                            <div>
                                <picture><source srcset="./img/complex3.webp" type="image/webp"><img src="./img/complex3.png" alt=""></picture>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper block-actions">
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
                                <div class="menu-info">
                                    <div class="dropdown">
                                        <button class="btn " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <picture><source srcset="./img/icon/copylinked.svg" type="image/webp"><img src="./img/icon/copylinked.svg" alt=""></picture>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <span>На сайте</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <span>На Rem.ua</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <span>Видео Youtube</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <span>	На карте</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="details-control">
                                <picture><source srcset="./img/icon/plus.svg" type="image/webp"><img src="./img/icon/plus.svg" alt=""></picture>
                            </button>
                        </div>
                    </td>
                </tr>
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
                        <div class="tbody-wrapper location">
                            <p><strong>ЖК «Южная Пальмира» </strong><span>2021-2024 г.</span></p>
                            <p>Генуэзская 5</p>
                            <span>Аркадия, Одесса, Одесский длинный</span>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper type">
                            <p>студии,</p>
                            <span>1 к, 2 к, 3 к, 4 к, 5</span>
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
                            <p>23-25</p>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper photo">
                            <picture><source srcset="./img/icon/default-foto.svg" type="image/webp"><img src="./img/icon/default-foto.svg" alt=""></picture>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper price">
                            <p>10 000 000</p>
                            <span>850/м <sup>2</sup></span>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper contact">
                            <div>
                                <p class="link-name" data-hover-contact>
                                    Федотов Василий
                                </p>
                                <p data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Гефест девелопер">
                                    Гефест девелопер
                                </p>
                                <a href="tel:380968796542">+380968796542</a>
                            </div>
                            <div>
                                <picture><source srcset="./img/complex3.webp" type="image/webp"><img src="./img/complex3.png" alt=""></picture>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper block-actions">
                            <div class="block-actions-wrapper">
                                <div class="menu-burger">
                                    <div class="dropdown">
                                        <button class="btn " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <picture><source srcset="./img/icon/burger-blue.svg" type="image/webp"><img src="./img/icon/burger-blue.svg" alt=""></picture>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Обновить</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="menu-info">
                                    <div class="dropdown">
                                        <button class="btn " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <picture><source srcset="./img/icon/copylinked.svg" type="image/webp"><img src="./img/icon/copylinked.svg" alt=""></picture>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <span>На сайте</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <span>На Rem.ua</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <span>Видео Youtube</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <span>	На карте</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="details-control">
                                <picture><source srcset="./img/icon/plus.svg" type="image/webp"><img src="./img/icon/plus.svg" alt=""></picture>
                            </button>
                        </div>
                    </td>
                </tr>
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
                        <div class="tbody-wrapper location">
                            <p><strong>ЖК «Южная Пальмира» </strong><span>2021-2024 г.</span></p>
                            <p>Генуэзская 5</p>
                            <span>Аркадия, Одесса, Одесский длинный</span>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper type">
                            <p>студии,</p>
                            <span>1 к, 2 к, 3 к, 4 к, 5</span>
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
                            <p>23-25</p>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper photo">
                            <picture><source srcset="./img/icon/default-foto.svg" type="image/webp"><img src="./img/icon/default-foto.svg" alt=""></picture>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper price">
                            <p>10 000 000</p>
                            <span>850/м <sup>2</sup></span>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper contact">
                            <div>
                                <p class="link-name" data-hover-contact>
                                    Федотов Василий
                                </p>
                                <p data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Гефест девелопер">
                                    Гефест девелопер
                                </p>
                                <a href="tel:380968796542">+380968796542</a>
                            </div>
                            <div>
                                <picture><source srcset="./img/complex3.webp" type="image/webp"><img src="./img/complex3.png" alt=""></picture>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="tbody-wrapper block-actions">
                            <div class="block-actions-wrapper">
                                <div class="menu-burger">
                                    <div class="dropdown">
                                        <button class="btn " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <picture><source srcset="./img/icon/burger-blue.svg" type="image/webp"><img src="./img/icon/burger-blue.svg" alt=""></picture>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Редактировать</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="menu-info">
                                    <div class="dropdown">
                                        <button class="btn " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <picture><source srcset="./img/icon/copylinked.svg" type="image/webp"><img src="./img/icon/copylinked.svg" alt=""></picture>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <span>На сайте</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <span>На Rem.ua</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <span>Видео Youtube</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <span>	На карте</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="details-control">
                                <picture><source srcset="./img/icon/plus.svg" type="image/webp"><img src="./img/icon/plus.svg" alt=""></picture>
                            </button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <!-- кінець table	-->
        </div>
    </div>
    <!-- кінець main	-->
</main>


<!-- кінець цей блок ще в розробці _modal	-->
<script src="./js/lib/popper.v2.11.8.min.js"></script>
<script src="./js/lib/bootstrap.v5.3.3.min.js"></script>
<script src="./js/lib/jquery.v3.7.1.min.js"></script>
<script src="./js/lib/data-tables.min.js"></script>
<script src="./js/lib/moment.min.js"></script>
<script src="./js/lib/data-range-picker.min.js"></script>
<script src="./js/lib/select2.min.js"></script>
<!-- для роботи Гео Модалки 27.08.2025 -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-text-icon@1.0.0/dist/leaflet.text-icon.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script src="./js/pages/modal-geo.min.js"></script>

<!-- для роботи Гео Модалки 27.08.2025 -->
<script src="./js/pages/complexes/index/page-complex-table.js"></script>

<script src="./js/pages/filter1.min.js"></script>
<script src="./js/pages/full-filter.min.js"></script>
<script src="./js/pages/my-dropdown.min.js"></script>
<script src="./js/pages/info-agent-or-contact-modal.min.js" type="module"></script>

{{--<script src="./js/pages/page-complex.js" type="module"></script>--}}

<script src="./js/pages/complexes/index/page-complex.js" type="module"></script>
</body>
</html>
