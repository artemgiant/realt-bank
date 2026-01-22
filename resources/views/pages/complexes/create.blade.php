<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Page-create-complex</title>
    <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/lib/select2.min.css">
    <link rel="stylesheet" href="./css/lib/bootstrap.v5.3.3.min.css">
    <link rel="stylesheet" href="./css/lib/data-range-picker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/tui-image-editor/3.15.0/tui-image-editor.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/tui-color-picker/2.2.6/tui-color-picker.min.css">
    <link rel="stylesheet" href="./css/lib/fancybox.min.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />

    <link rel="stylesheet" href="./css/pages/complexes/create/page-create-complex.css">
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
        <div class="create">
            <header class="create-header">
                <div class="create-header-left">
                    <a class="create-header-back" href="#">
                        <picture><source srcset="./img/icon/arrow-back-link.svg" type="image/webp"><img src="./img/icon/arrow-back-link.svg" alt=""></picture>
                    </a>
                    <h2 class="create-header-title">
                        Комплекс
                        <span>
						ID1234567
					</span>
                    </h2>
                </div>
                <div class="create-header-right">
                    <div class="create-header-add">
                        Добавлено:
                        <span>
						01.02.2025
					</span>
                    </div>
                    <div class="create-header-update">
                        Обновлено:
                        <span>
						10.02.2025
					</span>
                    </div>
                </div>
            </header>
            <div class="create-filter">
                <h3 class="create-filter-title">
                    <span>Общая информация</span>
                </h3>
                <div class="create-filter-wrapper">
                    <div class="create-filter-left">
                        <!-- Перша група табів -->
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
                                                <label class="green" for="name-complex-ua">Назва комплексу</label>
                                                <div class="item-inputText-wrapper">
                                                    <input class="item-inputText" type="text"
                                                           data-input-lang="ua" id="name-complex-ua" autocomplete="off"
                                                           name="name-complex-ua"
                                                           placeholder="Назва">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show active" id="ru-tab-pane" role="tabpanel"
                                         aria-labelledby="ru-tab" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label class="green" for="name-complex-ru">Название комплекса</label>
                                                <div class="item-inputText-wrapper">
                                                    <input class="item-inputText" type="text"
                                                           data-input-lang="ru" id="name-complex-ru" autocomplete="off"
                                                           name="name-complex-ru"
                                                           placeholder="Название">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="en-tab-pane" role="tabpanel"
                                         aria-labelledby="en-tab" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label class="green" for="name-complex-en">Name of the complex</label>
                                                <div class="item-inputText-wrapper">
                                                    <input class="item-inputText" type="text"
                                                           data-input-lang="en" id="name-complex-en" autocomplete="off"
                                                           name="name-complex-en"
                                                           placeholder="The name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Друга група табів -->
                        <div class="item">
                            <div class="tab-the-name">
                                <ul class="nav nav-tabs" id="tab-about-developer" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="ua-tab-about-developer" data-bs-toggle="tab"
                                                data-bs-target="#ua-tab-pane-about-developer" type="button" role="tab"
                                                aria-controls="ua-tab-pane-about-developer" aria-selected="false">UA
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="ru-tab-about-developer" data-bs-toggle="tab"
                                                data-bs-target="#ru-tab-pane-about-developer" type="button" role="tab"
                                                aria-controls="ru-tab-pane-about-developer" aria-selected="true">RU
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="en-tab-about-developer" data-bs-toggle="tab"
                                                data-bs-target="#en-tab-pane-about-developer" type="button" role="tab"
                                                aria-controls="en-tab-pane-about-developer" aria-selected="false">EN
                                        </button>
                                    </li>

                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade" id="ua-tab-pane-about-developer" role="tabpanel"
                                         aria-labelledby="ua-tab-about-developer" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label for="description-complex-ua">Опис комплексу</label>
                                                <div class="item-inputText-wrapper">
												<textarea class="item-textareaText" type="text"
                                                          data-input-lang="ua" id="description-complex-ua"
                                                          autocomplete="off"
                                                          name="description-complex-ua"
                                                          placeholder="Введіть текст"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show active" id="ru-tab-pane-about-developer" role="tabpanel"
                                         aria-labelledby="ru-tab-about-developer" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label for="description-complex-ru">Описание комплекса</label>
                                                <div class="item-inputText-wrapper">
												<textarea class="item-textareaText" type="text"
                                                          data-input-lang="ru" id="description-complex-ru"
                                                          autocomplete="off"
                                                          name="description-complex-ru"
                                                          placeholder="Введите текст"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="en-tab-pane-about-developer" role="tabpanel"
                                         aria-labelledby="en-tab-about-developer" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label for="description-complex-en">Description of the complex</label>
                                                <div class="item-inputText-wrapper">
												<textarea class="item-textareaText" type="text"
                                                          data-input-lang="en" id="description-complex-en"
                                                          autocomplete="off"
                                                          name="description-complex-en"
                                                          placeholder="Enter text"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="create-filter-right">
                        <div class="item">
                            <ul class="block-info">
                                <li class="block-info-item">
                                    <div class="info-title-wrapper">
                                        <h2 class="info-title">Контакт</h2>
                                        <button class="btn  btn-edit-client" type="button" data-bs-toggle="modal"
                                                data-bs-target="#add-contact-modal">
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                        d="M2.33398 10.9996H5.16065C5.24839 11.0001 5.33536 10.9833 5.41659 10.9501C5.49781 10.917 5.57169 10.8681 5.63398 10.8063L10.2473 6.1863L12.1406 4.33297C12.2031 4.27099 12.2527 4.19726 12.2866 4.11602C12.3204 4.03478 12.3378 3.94764 12.3378 3.85963C12.3378 3.77163 12.3204 3.68449 12.2866 3.60325C12.2527 3.52201 12.2031 3.44828 12.1406 3.3863L9.31398 0.5263C9.25201 0.463815 9.17828 0.414219 9.09704 0.380373C9.0158 0.346527 8.92866 0.329102 8.84065 0.329102C8.75264 0.329102 8.66551 0.346527 8.58427 0.380373C8.50303 0.414219 8.42929 0.463815 8.36732 0.5263L6.48732 2.41297L1.86065 7.03297C1.79886 7.09526 1.74998 7.16914 1.7168 7.25036C1.68363 7.33159 1.66681 7.41856 1.66732 7.5063V10.333C1.66732 10.5098 1.73756 10.6793 1.86258 10.8044C1.9876 10.9294 2.15717 10.9996 2.33398 10.9996ZM8.84065 1.93963L10.7273 3.8263L9.78065 4.77297L7.89398 2.8863L8.84065 1.93963ZM3.00065 7.77963L6.95398 3.8263L8.84065 5.71297L4.88732 9.6663H3.00065V7.77963ZM13.0007 12.333H1.00065C0.82384 12.333 0.654271 12.4032 0.529246 12.5282C0.404222 12.6533 0.333984 12.8228 0.333984 12.9996C0.333984 13.1764 0.404222 13.346 0.529246 13.471C0.654271 13.5961 0.82384 13.6663 1.00065 13.6663H13.0007C13.1775 13.6663 13.347 13.5961 13.4721 13.471C13.5971 13.346 13.6673 13.1764 13.6673 12.9996C13.6673 12.8228 13.5971 12.6533 13.4721 12.5282C13.347 12.4032 13.1775 12.333 13.0007 12.333Z"
                                                        fill="#AAAAAA"/>
                                            </svg>
                                        </button>
                                        <button class="btn  btn-add-client" type="button" data-bs-toggle="modal"
                                                data-bs-target="#add-contact-modal">
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                        d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z"
                                                        fill="#AAAAAA"/>
                                                <path
                                                        d="M8.583 13.5848C8.46922 13.471 8.39889 13.3139 8.39889 13.1403L8.39889 4.91816C8.39883 4.571 8.68022 4.28961 9.02739 4.28967C9.37449 4.28967 9.65588 4.57106 9.65588 4.91816L9.65588 13.1403C9.65583 13.4875 9.37449 13.7688 9.02733 13.7689C8.85389 13.7689 8.69678 13.6985 8.583 13.5848Z"
                                                        fill="#AAAAAA"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="info-avatar">
                                        <picture><source srcset="./img/icon/default-avatar-table.svg" type="image/webp"><img src="./img/icon/default-avatar-table.svg" alt=""></picture>
                                    </div>
                                    <div class="info-contacts">
                                        <p class="info-contacts-name">Василий Федотов</p>
                                        <p class="info-description">Представитель девелопера</p>
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
                        <div class="loading-documents loading-logo">
                            <label for="document-logo">
                                <input type="file" id="document-logo" name="document-logo"
                                       accept="image/png, image/jpeg, application/pdf">
                                <span>
								<svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
									<path
                                            d="M8.50627 13.2938C7.81303 13.9437 6.89417 14.2986 5.94403 14.2833C4.99388 14.2679 4.08694 13.8837 3.41499 13.2117C2.74305 12.5398 2.35879 11.6329 2.34348 10.6827C2.32817 9.73256 2.683 8.81371 3.33294 8.12046L9.17294 2.28713C9.52011 1.94269 9.96138 1.70858 10.4412 1.61425C10.9211 1.51991 11.4181 1.56956 11.8699 1.75695C12.3216 1.94433 12.7078 2.2611 12.98 2.6674C13.2522 3.0737 13.3982 3.55141 13.3996 4.04046C13.3992 4.36567 13.3342 4.68757 13.2083 4.98743C13.0824 5.28729 12.8982 5.55912 12.6663 5.78713L7.11294 11.3338C6.93932 11.4722 6.72095 11.5421 6.49924 11.5302C6.27753 11.5183 6.06789 11.4254 5.91008 11.2692C5.75226 11.1131 5.65723 10.9044 5.64302 10.6828C5.6288 10.4613 5.69638 10.2422 5.83294 10.0671L11.3929 4.50713L10.4463 3.56713L4.88627 9.12713C4.48502 9.55082 4.26495 10.1144 4.27289 10.6978C4.28082 11.2813 4.51614 11.8387 4.92876 12.2513C5.34138 12.6639 5.89874 12.8992 6.48222 12.9072C7.0657 12.9151 7.62925 12.695 8.05294 12.2938L13.6196 6.73379C14.3357 6.01859 14.7383 5.04822 14.739 4.03615C14.7396 3.02408 14.3381 2.05321 13.6229 1.33713C12.9077 0.621043 11.9374 0.218399 10.9253 0.217774C9.91323 0.217149 8.94236 0.618593 8.22627 1.33379L2.38627 7.18046C1.48313 8.1234 0.985245 9.38258 0.999314 10.6882C1.01338 11.9938 1.53828 13.2419 2.46154 14.1652C3.38479 15.0885 4.63295 15.6133 5.93855 15.6274C7.24416 15.6415 8.50334 15.1436 9.44627 14.2405L14.7263 8.95379L13.7863 8.00046L8.50627 13.2938Z"
                                            fill="#3585F5"/>
								</svg>
								<span class="text">
									Загрузить лого
								</span>
							</span>
                            </label>
                            <div class="filter-tags" data-render-document></div>
                            <div class="error-container"></div>
                        </div>
                    </div>
                    <div class="create-filter-row">
                        <div class="item">
                            <label class="item-label" for="select-developer">Девелопер</label>
                            <select id="select-developer" class="js-example-responsive3 my-select2" autocomplete="off">
                                <option></option>
                                <option>
                                    Девелопер 1
                                </option>
                            </select>
                        </div>
                        <div class="item">
                            <label for="cite-complex">Сайт комплекса</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText " type="url" id="cite-complex" name="cite-complex"
                                       autocomplete="off" placeholder="https://linkname.com">
                            </div>
                        </div>
                        <div class="item w50">
                            <label for="note-for-agents">Примечание для агентов</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText " type="text" id="note-for-agents" autocomplete="off"
                                       placeholder="Введите текст">
                            </div>
                        </div>
                        <div class="item">
                            <label for="cite-company">Сайт компании</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText " type="url" id="cite-company" name="cite-company"
                                       autocomplete="off" placeholder="https://linkname.com">
                            </div>
                        </div>
                        <div class="item">
                            <label for="developer-materials">Материалы девелопера</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText " type="url" id="developer-materials"
                                       name="developer-materials" autocomplete="off" placeholder="https://linkname.com">
                            </div>
                        </div>
                        <div class="item w50">
                            <label for="special-conditions">Специальные условия (Акции и скидки для сайта)</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText " type="text" id="special-conditions" name="special-conditions"
                                       autocomplete="off" placeholder="Введите текст">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="create-filter-documents">


                    <div class="loading-plan">
                        <label for="loading-plan">
                            <input type="file" id="loading-plan" name="loading-plan" multiple
                                   accept="image/png, image/jpeg">
                            <span>
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
							    <path
                                        d="M8.50627 13.2938C7.81303 13.9437 6.89417 14.2986 5.94403 14.2833C4.99388 14.2679 4.08694 13.8837 3.41499 13.2117C2.74305 12.5398 2.35879 11.6329 2.34348 10.6827C2.32817 9.73256 2.683 8.81371 3.33294 8.12046L9.17294 2.28713C9.52011 1.94269 9.96138 1.70858 10.4412 1.61425C10.9211 1.51991 11.4181 1.56956 11.8699 1.75695C12.3216 1.94433 12.7078 2.2611 12.98 2.6674C13.2522 3.0737 13.3982 3.55141 13.3996 4.04046C13.3992 4.36567 13.3342 4.68757 13.2083 4.98743C13.0824 5.28729 12.8982 5.55912 12.6663 5.78713L7.11294 11.3338C6.93932 11.4722 6.72095 11.5421 6.49924 11.5302C6.27753 11.5183 6.06789 11.4254 5.91008 11.2692C5.75226 11.1131 5.65723 10.9044 5.64302 10.6828C5.6288 10.4613 5.69638 10.2422 5.83294 10.0671L11.3929 4.50713L10.4463 3.56713L4.88627 9.12713C4.48502 9.55082 4.26495 10.1144 4.27289 10.6978C4.28082 11.2813 4.51614 11.8387 4.92876 12.2513C5.34138 12.6639 5.89874 12.8992 6.48222 12.9072C7.0657 12.9151 7.62925 12.695 8.05294 12.2938L13.6196 6.73379C14.3357 6.01859 14.7383 5.04822 14.739 4.03615C14.7396 3.02408 14.3381 2.05321 13.6229 1.33713C12.9077 0.621043 11.9374 0.218399 10.9253 0.217774C9.91323 0.217149 8.94236 0.618593 8.22627 1.33379L2.38627 7.18046C1.48313 8.1234 0.985245 9.38258 0.999314 10.6882C1.01338 11.9938 1.53828 13.2419 2.46154 14.1652C3.38479 15.0885 4.63295 15.6133 5.93855 15.6274C7.24416 15.6415 8.50334 15.1436 9.44627 14.2405L14.7263 8.95379L13.7863 8.00046L8.50627 13.2938Z"
                                        fill="#3585F5"/>
							</svg>
							<span class="text">
								Загрузить план
							</span>
						</span>
                        </label>
                        <div class="filter-tags" data-render-document></div>
                        <div class="error-container" data-error></div>
                    </div>
                </div>
                <div class="create-filter-photo">
                    <div class="photo-info">
                        <div class="photo-info-wrapper">
                            <span class="photo-info-title">Фото комплекса (первое фото будет обложкой объявления, перетяните фотографии чтобы поменять порядок)</span>
                            <div class="photo-info-wrapper-wrapper">
                                <ul class="photo-info-list">
                                    <li class="photo-info-btn-wrapper">
                                        <label class="photo-info-btn" for="loading-photo">
                                            <input type="file" id="loading-photo" name="loading-photo" multiple
                                                   accept="image/png, image/jpg, image/jpeg, image/heic">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                        d="M8.50725 13.2938C7.814 13.9437 6.89515 14.2986 5.945 14.2833C4.99486 14.2679 4.08791 13.8837 3.41597 13.2117C2.74403 12.5398 2.35977 11.6329 2.34446 10.6827C2.32914 9.73256 2.68398 8.81371 3.33392 8.12046L9.17392 2.28713C9.52109 1.94269 9.96235 1.70858 10.4422 1.61425C10.9221 1.51991 11.4191 1.56956 11.8708 1.75695C12.3226 1.94433 12.7088 2.2611 12.981 2.6674C13.2532 3.0737 13.3992 3.55141 13.4006 4.04046C13.4002 4.36567 13.3352 4.68757 13.2093 4.98743C13.0834 5.28729 12.8992 5.55912 12.6672 5.78713L7.11392 11.3338C6.94029 11.4722 6.72193 11.5421 6.50022 11.5302C6.27851 11.5183 6.06887 11.4254 5.91105 11.2692C5.75324 11.1131 5.65821 10.9044 5.64399 10.6828C5.62977 10.4613 5.69735 10.2422 5.83392 10.0671L11.3939 4.50713L10.4472 3.56713L4.88725 9.12713C4.486 9.55082 4.26593 10.1144 4.27387 10.6978C4.2818 11.2813 4.51712 11.8387 4.92974 12.2513C5.34236 12.6639 5.89971 12.8992 6.4832 12.9072C7.06668 12.9151 7.63022 12.695 8.05392 12.2938L13.6206 6.73379C14.3367 6.01859 14.7393 5.04822 14.7399 4.03615C14.7406 3.02408 14.3391 2.05321 13.6239 1.33713C12.9087 0.621043 11.9383 0.218399 10.9263 0.217774C9.9142 0.217149 8.94333 0.618593 8.22725 1.33379L2.38725 7.18046C1.4841 8.1234 0.986222 9.38258 1.00029 10.6882C1.01436 11.9938 1.53926 13.2419 2.46251 14.1652C3.38577 15.0885 4.63393 15.6133 5.93953 15.6274C7.24513 15.6415 8.50431 15.1436 9.44725 14.2405L14.7272 8.95379L13.7872 8.00046L8.50725 13.2938Z"
                                                        fill="#3585F5"/>
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
                    </div>
                    <div class="create-filter-row">


{{--                        #TODO добавить локацию ТАКУЮ КАК В  resources/views/pages/properties/particles/create/_location_block.blade.php--}}

                        <div class="item w20">
                            <label class="item-label" for="level-house">Класс жилья</label>
                            <select id="level-house" class="js-example-responsive3 my-select2" autocomplete="off">
                                <option></option>
                                <option>
                                    Класс жилья 1
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="create-filter-row">
                        <div class="item w15 blue-select2">
                            <label class="item-label" for="category">Категория</label>
                            <select id="category" class="js-example-responsive2" autocomplete="off">
                                <option></option>
                                <option>
                                    Категория 1
                                </option>
                                <option>
                                    Категория 2
                                </option>
                                <option>
                                    Категория 3
                                </option>
                            </select>
                        </div>
                        <div class="item w20">
						<span>
							<label class="item-label" for="type-object">Типы объектов </label>
							/
							<label class="item-label" for="count-object">Количество</label>
						</span>
                            <div class="item-action-wrapper">
                                <select id="type-object" class="js-example-responsive2" autocomplete="off">
                                    <option></option>
                                    <option>
                                        объект 1
                                    </option>
                                    <option>
                                        объект 2
                                    </option>
                                    <option>
                                        объект 3
                                    </option>
                                </select>
                                /
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" type="text" id="count-object" name="count-object"
                                           autocomplete="off" placeholder="0">
                                </div>
                            </div>
                        </div>
                        <div class="item w10">
                            <span class="item-label">Состояние</span>
                            <div class="multiple-menu">
                                <button class="multiple-menu-btn" data-open-menu="false">
                                    Выберите
                                </button>
                                <div class="multiple-menu-wrapper">
                                    <ul class="multiple-menu-list">
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="new-buildings">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">Новостройки</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="khrushchevka">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">Хрущёвка</span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="item w10">
                            <label class="item-label" for="all-area">Площадь общая</label>
                            <div class="item-inputText-wrapper shtrih">
                                <input class="item-inputText" id="all-area" type="text" placeholder="От" autocomplete="off">
                                <input class="item-inputText" type="text" placeholder="До" autocomplete="off">
                            </div>
                        </div>
                        <div class="item w15">
						<span>
							<label class="item-label" for="price-object-m2">Цена от за м²</label>
							/
							<label class="item-label" for="object">Объект</label>
						</span>
                            <div class="item-action-wrapper">
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" type="text" id="price-object-m2" name="count-object"
                                           autocomplete="off" placeholder="м²">
                                </div>
                                /
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" type="text" id="object" name="count-object"
                                           autocomplete="off" placeholder="100 000 000">
                                </div>
                            </div>
                        </div>
                        <div class="item w10">
                            <label class="item-label" for="full-filter-currency"></label>
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
                        <div class="item w15">
                            <span class="item-label">Особенности</span>
                            <div class="multiple-menu">
                                <button class="multiple-menu-btn" data-open-menu="false">
                                    Выберите параметры
                                </button>
                                <div class="multiple-menu-wrapper">
                                    <label>
                                        <input class="multiple-menu-search" autocomplete="off" name="search-additionally" type="text" placeholder="Поиск">
                                    </label>
                                    <ul class="multiple-menu-list">
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="from-the-intermediary">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">От посредника</span>
                                            </label>
                                        </li>
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="state-programs">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">Госпрограммы</span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="create-filter-tags">
                        <div class="filter-tags">
                            <div class="badge rounded-pill qwe1">
                                Параметр из фильтра
                                <button type="button" aria-label="Close">
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path
                                                d="M0.878207 9.87891C0.653517 9.87891 0.428717 9.79318 0.257263 9.62173C-0.0857544 9.27871 -0.0857544 8.72285 0.257263 8.37984L8.37992 0.257181C8.72294 -0.085727 9.27879 -0.085727 9.62181 0.257181C9.96472 0.600089 9.96472 1.15605 9.62181 1.49896L1.49915 9.62162C1.3277 9.79318 1.1029 9.87891 0.878207 9.87891Z"
                                                fill="#AAAAAA"/>
                                        <path
                                                d="M9.00086 9.8788C8.77606 9.8788 8.55137 9.79307 8.37992 9.62162L0.257263 1.49896C-0.0857544 1.15605 -0.0857544 0.600089 0.257263 0.257181C0.600171 -0.085727 1.15613 -0.085727 1.49904 0.257181L9.6217 8.37984C9.96461 8.72285 9.96461 9.27871 9.6217 9.62173C9.45035 9.79307 9.22566 9.8788 9.00086 9.8788Z"
                                                fill="#AAAAAA"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="badge rounded-pill qwe2">
                                Параметр из фильтра
                                <button type="button" aria-label="Close">
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path
                                                d="M0.878207 9.87891C0.653517 9.87891 0.428717 9.79318 0.257263 9.62173C-0.0857544 9.27871 -0.0857544 8.72285 0.257263 8.37984L8.37992 0.257181C8.72294 -0.085727 9.27879 -0.085727 9.62181 0.257181C9.96472 0.600089 9.96472 1.15605 9.62181 1.49896L1.49915 9.62162C1.3277 9.79318 1.1029 9.87891 0.878207 9.87891Z"
                                                fill="#AAAAAA"/>
                                        <path
                                                d="M9.00086 9.8788C8.77606 9.8788 8.55137 9.79307 8.37992 9.62162L0.257263 1.49896C-0.0857544 1.15605 -0.0857544 0.600089 0.257263 0.257181C0.600171 -0.085727 1.15613 -0.085727 1.49904 0.257181L9.6217 8.37984C9.96461 8.72285 9.96461 9.27871 9.6217 9.62173C9.45035 9.79307 9.22566 9.8788 9.00086 9.8788Z"
                                                fill="#AAAAAA"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="badge rounded-pill qwe2">
                                Параметр из фильтра
                                <button type="button" aria-label="Close">
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path
                                                d="M0.878207 9.87891C0.653517 9.87891 0.428717 9.79318 0.257263 9.62173C-0.0857544 9.27871 -0.0857544 8.72285 0.257263 8.37984L8.37992 0.257181C8.72294 -0.085727 9.27879 -0.085727 9.62181 0.257181C9.96472 0.600089 9.96472 1.15605 9.62181 1.49896L1.49915 9.62162C1.3277 9.79318 1.1029 9.87891 0.878207 9.87891Z"
                                                fill="#AAAAAA"/>
                                        <path
                                                d="M9.00086 9.8788C8.77606 9.8788 8.55137 9.79307 8.37992 9.62162L0.257263 1.49896C-0.0857544 1.15605 -0.0857544 0.600089 0.257263 0.257181C0.600171 -0.085727 1.15613 -0.085727 1.49904 0.257181L9.6217 8.37984C9.96461 8.72285 9.96461 9.27871 9.6217 9.62173C9.45035 9.79307 9.22566 9.8788 9.00086 9.8788Z"
                                                fill="#AAAAAA"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>


                    <div class="create-filter-locations">
                        {{--                    #todo ТУТ СОЗДАЮТЬСЯ БЛОКИ ДО КОМПЛЕСОВ, МОЖНА СОЗДАТЬ НЕСКОЛЬКО БЛОКОВ ДО ОДНОГО КОМПЛЕКСА --}}

                        <ul class="create-filter-locations-list">
                            <li class="create-filter-locations-item">
                                <div class="item w15">
                                    <label class="green" for="section-or-building">Секция / Корпус</label>
                                    <div class="item-inputText-wrapper">
                                        <input class="item-inputText" type="text" id="section-or-building" name="number-house"
                                               autocomplete="off" placeholder="Введите текст">
                                    </div>
                                    <div class="add_new-tel">
                                        <button type="button" class="btn btn-new-tel">
                                            <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z" fill="#3585F5" />
                                                <path d="M4.583 9.58476C4.46922 9.47098 4.39889 9.31387 4.39889 9.14032L4.39889 0.918164C4.39883 0.571001 4.68022 0.289614 5.02739 0.28967C5.37449 0.28967 5.65588 0.571056 5.65588 0.918164L5.65588 9.14032C5.65583 9.48748 5.37449 9.76881 5.02733 9.76887C4.85389 9.76887 4.69678 9.69853 4.583 9.58476Z" fill="#3585F5" />
                                            </svg>
                                            <!--									ця svg коли треба видалити вона з - -->
                                            <!--										<svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">-->
                                            <!--											<path d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z" fill="#3585F5" />-->
                                            <!--										</svg>-->
                                        </button>
                                    </div>
                                </div>
                                <div class="item w15">
{{--                                    #TODO  улицы показываються только из населенного пункта который выбраный в комплексе--}}
                                    <label class="item-label" for="street-1">Улица</label>
                                    <select id="street-1" class="js-example-responsive3 my-select2" autocomplete="off">
                                        <option></option>
                                        <option>
                                            street 1
                                        </option>
                                    </select>
                                </div>
                                <div class="item w7-5">
                                    <label for="number-house-1">№ Дом</label>
                                    <div class="item-inputText-wrapper">
                                        <input class="item-inputText" type="text" id="number-house-1" name="number-house-1"
                                               autocomplete="off" placeholder="Номер">
                                    </div>
                                </div>
                                <div class="item w7-5">
                                    <label for="floor-1">Этажность</label>
                                    <div class="item-inputText-wrapper">
                                        <input class="item-inputText" type="text" id="floor-1" name="floor-1"
                                               autocomplete="off" placeholder="Номер">
                                    </div>
                                </div>
                                <div class="item w10">
                                    <label class="item-label" for="year-completion-1">Год сдачи</label>
                                    <select id="year-completion-1" class="js-example-responsive3 my-select2" autocomplete="off">
                                        <option></option>
                                        <option>
                                            1999
                                        </option>
                                    </select>
                                </div>
                                <div class="item w15">
                                    <label class="item-label" for="heating-1">Отопление</label>
                                    <select id="heating-1" class="js-example-responsive3 my-select2" autocomplete="off">
                                        <option></option>
                                        <option>
                                            Отопление 1
                                        </option>
                                    </select>
                                </div>
                                <div class="item w15">
                                    <label class="item-label" for="wall-type-1">Тип стен</label>
                                    <select id="wall-type-1" class="js-example-responsive3 my-select2" autocomplete="off">
                                        <option></option>
                                        <option>
                                            Монолит 1
                                        </option>
                                    </select>
                                </div>
                                <div class="item w10">
                                    <div class="loading-plan" data-plan-id="plan-1">
                                        <label for="loading-plan-1">
                                            <input type="file" id="loading-plan-1" name="loading-plan-1" multiple
                                                   accept="image/png, image/jpeg">
                                            <span>
											<svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
												<path
                                                        d="M8.50627 13.2938C7.81303 13.9437 6.89417 14.2986 5.94403 14.2833C4.99388 14.2679 4.08694 13.8837 3.41499 13.2117C2.74305 12.5398 2.35879 11.6329 2.34348 10.6827C2.32817 9.73256 2.683 8.81371 3.33294 8.12046L9.17294 2.28713C9.52011 1.94269 9.96138 1.70858 10.4412 1.61425C10.9211 1.51991 11.4181 1.56956 11.8699 1.75695C12.3216 1.94433 12.7078 2.2611 12.98 2.6674C13.2522 3.0737 13.3982 3.55141 13.3996 4.04046C13.3992 4.36567 13.3342 4.68757 13.2083 4.98743C13.0824 5.28729 12.8982 5.55912 12.6663 5.78713L7.11294 11.3338C6.93932 11.4722 6.72095 11.5421 6.49924 11.5302C6.27753 11.5183 6.06789 11.4254 5.91008 11.2692C5.75226 11.1131 5.65723 10.9044 5.64302 10.6828C5.6288 10.4613 5.69638 10.2422 5.83294 10.0671L11.3929 4.50713L10.4463 3.56713L4.88627 9.12713C4.48502 9.55082 4.26495 10.1144 4.27289 10.6978C4.28082 11.2813 4.51614 11.8387 4.92876 12.2513C5.34138 12.6639 5.89874 12.8992 6.48222 12.9072C7.0657 12.9151 7.62925 12.695 8.05294 12.2938L13.6196 6.73379C14.3357 6.01859 14.7383 5.04822 14.739 4.03615C14.7396 3.02408 14.3381 2.05321 13.6229 1.33713C12.9077 0.621043 11.9374 0.218399 10.9253 0.217774C9.91323 0.217149 8.94236 0.618593 8.22627 1.33379L2.38627 7.18046C1.48313 8.1234 0.985245 9.38258 0.999314 10.6882C1.01338 11.9938 1.53828 13.2419 2.46154 14.1652C3.38479 15.0885 4.63295 15.6133 5.93855 15.6274C7.24416 15.6415 8.50334 15.1436 9.44627 14.2405L14.7263 8.95379L13.7863 8.00046L8.50627 13.2938Z"
                                                        fill="#3585F5"/>
											</svg>
											<span class="text">
												Загрузить план
											</span>
										</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="item w100" data-plan-id="plan-file-error-1">
                                    <div class="filter-tags" data-render-document></div>
                                    <div class="error-container" data-error></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="create-btnGroup">
                        <div class="create-btnGroup-wrapper">
                            <div class="create-btnGroup-left">
                                <button class="btn btn-outline-primary" type="button">
                                    Отменить изменения
                                </button>
                                <button class="btn btn-outline-danger" type="button">
                                    Удалить комплекс
                                </button>
                            </div>
                            <div class="create-btnGroup-right">
                                <button class="btn btn-primary" type="submit">
                                    Сохранить изменения
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- кінець main	-->
</main>


{{--#TODO ДОБАВИТЬ МОДАЛЬНОЕ ОКНО ТАКОЕ КАК В developers resources/views/pages/developers/modals/contact-modal.blade.php--}}

<!-- кінець цей блок ще в розробці _modal	-->
<script src="./js/lib/popper.v2.11.8.min.js"></script>
<script src="./js/lib/bootstrap.v5.3.3.min.js"></script>
<script src="./js/lib/jquery.v3.7.1.min.js"></script>
<script src="./js/lib/select2.min.js"></script>
<script src="./js/lib/moment.min.js"></script>
<script src="./js/lib/data-range-picker.min.js"></script>
<script src="./js/lib/fancybox.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<!-- Спочатку залежності -->
<script src="./js/lib/tui-code-snippet.min.js"></script>
<script src="./js/lib/fabric.min.js"></script>
<script src="./js/lib/tui-color-picker.min.js"></script>
<!-- Потім основний редактор -->
<script src="./js/lib/tui-image-editor.min.js"></script>
<script src="./js/lib/heic2any.min.js"></script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-text-icon@1.0.0/dist/leaflet.text-icon.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>




<script src="./js/pages/complexes/create/function_on_pages-create.js" type="module"></script>

<!--<script src="./js/pages/full-filter.min.js"></script>-->
<script src="./js/pages/complexes/create/page-create-complex.js" type="module"></script>
</body>
</html>
