<div class="filter">
    <div class="filter-header">
        {{-- Поиск по имени / Email / телефону --}}
        <label for="search-name-email-phone" class="input-search">
            <input id="search-name-email-phone" autocomplete="off" type="text" placeholder="Поиск по имени / Email / телефону">
        </label>

        {{-- Должность --}}
        <label for="position" class="blue-select2">
            <select id="position" class="js-example-responsive2 position">
                <option></option>
                @foreach($positions as $position)
                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                @endforeach
            </select>
        </label>

        {{-- Статус агента --}}
        <label for="statusagents">
            <select id="statusagents" class="js-example-responsive2 statusagents">
                <option></option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                @endforeach
            </select>
        </label>

        {{-- Компания --}}
        <label for="company">
            <select id="company" class="js-example-responsive2 company">
                <option></option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </label>

        {{-- Офис --}}
        <label for="offices">
            <select id="offices" class="js-example-responsive2 offices">
                <option></option>
                @foreach($offices as $office)
                    <option value="{{ $office->id }}">{{ $office->name }}</option>
                @endforeach
            </select>
        </label>

        {{-- Теги (множественный выбор) --}}
        <div class="item">
            <div class="multiple-menu">
                <button class="multiple-menu-btn" data-open-menu="false">
                    Теги
                </button>
                <div class="multiple-menu-wrapper">
                    <label>
                        <input class="multiple-menu-search" autocomplete="off" name="complex-search" type="text" placeholder="Поиск">
                    </label>
                    <ul class="multiple-menu-list">
                        <li class="multiple-menu-item">
                            <label class="my-custom-input">
                                <input data-name="checkbox-all" type="checkbox" name="complex-all" checked>
                                <span class="my-custom-box"></span>
                                <span class="my-custom-text">Все</span>
                            </label>
                        </li>
                        @foreach($tags as $tag)
                            <li class="multiple-menu-item">
                                <label class="my-custom-input">
                                    <input type="checkbox" name="{{ $tag->id }}">
                                    <span class="my-custom-box"></span>
                                    <span class="my-custom-text">{{ $tag->name }}</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- Date picker --}}
        <div class="data">
            <span>
                <input class="item-inputText date-piker" type="text" id="datapiker" autocomplete="off" placeholder="Выберите дату">
                <picture>
                    <source srcset="{{ asset('img/icon/calendar.svg') }}" type="image/webp">
                    <img src="{{ asset('img/icon/calendar.svg') }}" alt="">
                </picture>
            </span>
        </div>

        {{-- Кнопка сброса фильтров --}}
        <div class="header-btn">
            <div class="full-filter-btn-wrapper">
                <button class="btn btn-primary" disabled id="full-filter-btn">
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="#3585F5" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.878207 9.87891C0.653517 9.87891 0.428717 9.79318 0.257263 9.62173C-0.0857544 9.27871 -0.0857544 8.72285 0.257263 8.37984L8.37992 0.257181C8.72294 -0.085727 9.27879 -0.085727 9.62181 0.257181C9.96472 0.600089 9.96472 1.15605 9.62181 1.49896L1.49915 9.62162C1.3277 9.79318 1.1029 9.87891 0.878207 9.87891Z" fill="#3585F5"></path>
                        <path d="M9.00086 9.8788C8.77606 9.8788 8.55137 9.79307 8.37992 9.62162L0.257263 1.49896C-0.0857544 1.15605 -0.0857544 0.600089 0.257263 0.257181C0.600171 -0.085727 1.15613 -0.085727 1.49904 0.257181L9.6217 8.37984C9.96461 8.72285 9.96461 9.27871 9.6217 9.62173C9.45035 9.79307 9.22566 9.8788 9.00086 9.8788Z" fill="#3585F5"></path>
                    </svg>
                </button>
            </div>
        </div>




    </div>

</div>
