<form method="GET" action="#" id="filter-form">
    <div class="filter">
        <div class="filter-header">

            {{-- Фильтр по локации --}}
            @include('pages.complexes.particles.index._location_filter')

            {{-- Категория --}}
            <label for="category_id" class="blue-select2">
                <select id="category_id" name="category_id" class="js-example-responsive2">
                    <option value="">Категория</option>
                    @foreach($complexCategories ?? [] as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>

            {{-- Цена --}}
            <div class="header-price">
                <label for="price_from">
                    <input class="form-control" type="text" id="price_from" name="price_from" autocomplete="off" placeholder="Цена от">
                </label>
                <label for="price_to">
                    <input class="form-control" type="text" id="price_to" name="price_to" autocomplete="off" placeholder="Цена до">
                </label>
                <label for="currency_id">
                    <select id="currency_id" name="currency_id" class="js-example-responsive3">
                        <option value="USD" selected>USD</option>
                        <option value="UAH">UAH</option>
                        <option value="EUR">EUR</option>
                    </select>
                </label>
            </div>

            <div class="header-btn">
                {{-- Кнопка расширенного фильтра --}}
                <div class="full-filter-btn-wrapper">
                    <button class="btn btn-primary" id="full-filter-btn" type="button">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9 6.2C8.08562 6.2 7.30774 5.61561 7.01947 4.79994L2.7 4.8C2.3134 4.8 2 4.4866 2 4.1C2 3.7134 2.3134 3.4 2.7 3.4L7.01972 3.39937C7.30818 2.58406 8.08588 2 9 2C9.91412 2 10.6918 2.58406 10.9803 3.39937L15.3 3.4C15.6866 3.4 16 3.7134 16 4.1C16 4.4866 15.6866 4.8 15.3 4.8L10.9805 4.79994C10.6923 5.61561 9.91438 6.2 9 6.2ZM9 4.8C9.3866 4.8 9.7 4.4866 9.7 4.1C9.7 3.7134 9.3866 3.4 9 3.4C8.6134 3.4 8.3 3.7134 8.3 4.1C8.3 4.4866 8.6134 4.8 9 4.8ZM4.1 11.1C2.9402 11.1 2 10.1598 2 9C2 7.8402 2.9402 6.9 4.1 6.9C5.01412 6.9 5.79182 7.48406 6.08028 8.29937L15.3 8.3C15.6866 8.3 16 8.6134 16 9C16 9.3866 15.6866 9.7 15.3 9.7L6.08053 9.69994C5.79226 10.5156 5.01438 11.1 4.1 11.1ZM4.1 9.7C4.4866 9.7 4.8 9.3866 4.8 9C4.8 8.6134 4.4866 8.3 4.1 8.3C3.7134 8.3 3.4 8.6134 3.4 9C3.4 9.3866 3.7134 9.7 4.1 9.7ZM13.9 16C12.9817 16 12.2011 15.4106 11.9158 14.5895C11.8784 14.5967 11.8396 14.6 11.8 14.6H2.7C2.3134 14.6 2 14.2866 2 13.9C2 13.5134 2.3134 13.2 2.7 13.2H11.8C11.8396 13.2 11.8784 13.2033 11.9162 13.2096C12.2011 12.3894 12.9817 11.8 13.9 11.8C15.0598 11.8 16 12.7402 16 13.9C16 15.0598 15.0598 16 13.9 16ZM13.9 14.6C14.2866 14.6 14.6 14.2866 14.6 13.9C14.6 13.5134 14.2866 13.2 13.9 13.2C13.5134 13.2 13.2 13.5134 13.2 13.9C13.2 14.2866 13.5134 14.6 13.9 14.6Z" fill="#3585F5"/>
                        </svg>
                    </button>
                    {{-- Счетчик активных фильтров --}}
                    <div class="full-filter-counter" style="display: none;">
                        <span>0</span>
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

        {{-- Теги активных фильтров --}}
        <div class="filter-tags">
            {{-- Теги будут добавляться динамически через JS --}}
        </div>

        {{-- Расширенный фильтр --}}
        <div class="full-filter">
            <h3 class="full-filter-title">
                <span>Расширенный фильтр</span>
            </h3>
            <div class="full-filter-wrapper">
                {{-- Первая строка фильтров --}}
                <div class="full-filter-row">
                    {{-- Девелопер --}}
                    <div class="item">
                        <label class="item-label" for="developer_id">Девелопер</label>
                        <select id="developer_id" name="developer_id" class="js-example-responsive2" autocomplete="off">
                            <option value="">Все</option>
                            @foreach($developers ?? [] as $developer)
                                <option value="{{ $developer->id }}">{{ $developer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Тип объекта --}}
                    <div class="item">
                        <span class="item-label">Тип объекта</span>
                        <div class="multiple-menu">
                            <button class="multiple-menu-btn" type="button" data-open-menu="false">
                                Все
                            </button>
                            <div class="multiple-menu-wrapper">
                                <ul class="multiple-menu-list">
                                    @foreach($objectTypes ?? [] as $objectType)
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="object_type_id[]" value="{{ $objectType->id }}">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">{{ $objectType->name }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Год сдачи --}}
                    <div class="item">
                        <span class="item-label">Год сдачи</span>
                        <div class="multiple-menu">
                            <button class="multiple-menu-btn" type="button" data-open-menu="false">
                                Все
                            </button>
                            <div class="multiple-menu-wrapper">
                                <ul class="multiple-menu-list">
                                    @foreach($yearsBuilt ?? [] as $year)
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="year_built[]" value="{{ $year->value }}">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">{{ $year->name }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Состояние --}}
                    <div class="item">
                        <span class="item-label">Состояние</span>
                        <div class="multiple-menu">
                            <button class="multiple-menu-btn" type="button" data-open-menu="false">
                                Все
                            </button>
                            <div class="multiple-menu-wrapper">
                                <ul class="multiple-menu-list">
                                    @foreach($conditions ?? [] as $condition)
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="condition_id[]" value="{{ $condition->id }}">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">{{ $condition->name }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>


                    {{-- Тип стен --}}
                    <div class="item">
                        <span class="item-label">Тип стен</span>
                        <div class="multiple-menu">
                            <button class="multiple-menu-btn" type="button" data-open-menu="false">
                                Все
                            </button>
                            <div class="multiple-menu-wrapper">
                                <ul class="multiple-menu-list">
                                    @foreach($wallTypes ?? [] as $wallType)
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="wall_type_id[]" value="{{ $wallType->id }}">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">{{ $wallType->name }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Отопление --}}
                    <div class="item">
                        <span class="item-label">Отопление</span>
                        <div class="multiple-menu">
                            <button class="multiple-menu-btn" type="button" data-open-menu="false">
                                Все
                            </button>
                            <div class="multiple-menu-wrapper">
                                <ul class="multiple-menu-list">
                                    @foreach($heatingTypes ?? [] as $heatingType)
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="heating_type_id[]" value="{{ $heatingType->id }}">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">{{ $heatingType->name }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>



                </div>

                {{-- Вторая строка фильтров --}}
                <div class="full-filter-row">




                </div>

                {{-- Третья строка фильтров --}}
                <div class="full-filter-row">



                    {{-- Этажность --}}
                    <div class="item">
                        <label class="item-label" for="floors_from">Этажность</label>
                        <div class="item-inputText-wrapper">
                            <input class="item-inputText" id="floors_from" name="floors_from" type="text" placeholder="От" autocomplete="off">
                            <input class="item-inputText" id="floors_to" name="floors_to" type="text" placeholder="До" autocomplete="off">
                        </div>
                    </div>

                    {{-- Класс недвижимости --}}
                    <div class="item">
                        <label class="item-label" for="housing_class_id">Класс недвижимости</label>
                        <select id="housing_class_id" name="housing_class_id" class="js-example-responsive2" autocomplete="off">
                            <option value="">Все</option>
                            @foreach($housingClasses ?? [] as $housingClass)
                                <option value="{{ $housingClass->id }}">{{ $housingClass->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Поиск по ID --}}
                    <div class="item search-on-id">
                        <label for="search_id">Поиск по ID</label>
                        <div class="item-inputSearch-wrapper">
                            <input class="item-inputSearch" type="text" autocomplete="off" id="search_id" name="search_id" placeholder="Поиск">
                            <button class="item-inputSearchBtn" type="button" id="search-id-btn">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.1171 16C15.0002 16.0003 14.8845 15.9774 14.7767 15.9327C14.6687 15.888 14.5707 15.8223 14.4884 15.7396L11.465 12.7218C10.224 13.6956 8.6916 14.224 7.11391 14.2222C5.70692 14.2222 4.33151 13.8052 3.16164 13.0238C1.99176 12.2424 1.07995 11.1318 0.541519 9.83244C0.00308508 8.53306 -0.137797 7.1032 0.136693 5.7238C0.411184 4.34438 1.08872 3.07731 2.08362 2.0828C3.07852 1.08829 4.34609 0.411022 5.72606 0.136639C7.106 -0.137743 8.53643 0.00308386 9.83632 0.541306C11.1362 1.07953 12.2472 1.99098 13.029 3.16039C13.8106 4.3298 14.2278 5.70467 14.2278 7.11111C14.231 8.69031 13.7023 10.2245 12.7268 11.4667L15.7458 14.4889C15.8679 14.6135 15.9508 14.7714 15.9839 14.9427C16.017 15.114 15.9988 15.2914 15.9318 15.4524C15.8647 15.6136 15.7517 15.7515 15.6069 15.8488C15.462 15.9462 15.2916 15.9988 15.1171 16ZM7.11391 1.77778C6.05867 1.77778 5.02712 2.09058 4.14971 2.67661C3.2723 3.26264 2.58844 4.0956 2.18462 5.07013C1.78079 6.04467 1.67513 7.11706 1.881 8.15155C2.08687 9.18613 2.59502 10.1364 3.34119 10.8823C4.08737 11.6283 5.03806 12.1362 6.07302 12.342C7.10796 12.5477 8.18073 12.4421 9.1557 12.0385C10.1307 11.6348 10.9639 10.9512 11.5502 10.0741C12.1364 9.19706 12.4493 8.16595 12.4493 7.11111C12.4477 5.69713 11.885 4.34154 10.8848 3.3417C9.88461 2.34186 8.52843 1.77943 7.11391 1.77778Z" fill="black"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Площадь --}}
                    <div class="item">
                        <label class="item-label" for="area_from">Площадь</label>
                        <div class="item-inputText-wrapper">
                            <input class="item-inputText" id="area_from" name="area_from" type="text" placeholder="От" autocomplete="off">
                            <input class="item-inputText" id="area_to" name="area_to" type="text" placeholder="До" autocomplete="off">
                        </div>
                    </div>

                    {{-- Цена за м² --}}
                    <div class="item">
                        <label class="item-label" for="price_per_m2_from">Цена за м²</label>
                        <div class="item-inputText-wrapper">
                            <input class="item-inputText" id="price_per_m2_from" name="price_per_m2_from" type="text" placeholder="От" autocomplete="off">
                            <input class="item-inputText" id="price_per_m2_to" name="price_per_m2_to" type="text" placeholder="До" autocomplete="off">
                        </div>
                    </div>

                    {{-- Особенности --}}
                    <div class="item">
                        <span class="item-label">Особенности</span>
                        <div class="multiple-menu">
                            <button class="multiple-menu-btn" type="button" data-open-menu="false">
                                Все
                            </button>
                            <div class="multiple-menu-wrapper">
                                <label>
                                    <input class="multiple-menu-search" autocomplete="off" name="search-features" type="text" placeholder="Поиск">
                                </label>
                                <ul class="multiple-menu-list">
                                    @foreach($features ?? [] as $feature)
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="features[]" value="{{ $feature->id }}">
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">{{ $feature->name }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</form>
