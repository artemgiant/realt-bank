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
                <input class="item-inputText date-piker" type="text" id="datapiker" autocomplete="off">
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
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9 6.2C8.08562 6.2 7.30774 5.61561 7.01947 4.79994L2.7 4.8C2.3134 4.8 2 4.4866 2 4.1C2 3.7134 2.3134 3.4 2.7 3.4L7.01972 3.39937C7.30818 2.58406 8.08588 2 9 2C9.91412 2 10.6918 2.58406 10.9803 3.39937L15.3 3.4C15.6866 3.4 16 3.7134 16 4.1C16 4.4866 15.6866 4.8 15.3 4.8L10.9805 4.79994C10.6923 5.61561 9.91438 6.2 9 6.2ZM9 4.8C9.3866 4.8 9.7 4.4866 9.7 4.1C9.7 3.7134 9.3866 3.4 9 3.4C8.6134 3.4 8.3 3.7134 8.3 4.1C8.3 4.4866 8.6134 4.8 9 4.8ZM4.1 11.1C2.9402 11.1 2 10.1598 2 9C2 7.8402 2.9402 6.9 4.1 6.9C5.01412 6.9 5.79182 7.48406 6.08028 8.29937L15.3 8.3C15.6866 8.3 16 8.6134 16 9C16 9.3866 15.6866 9.7 15.3 9.7L6.08053 9.69994C5.79226 10.5156 5.01438 11.1 4.1 11.1ZM4.1 9.7C4.4866 9.7 4.8 9.3866 4.8 9C4.8 8.6134 4.4866 8.3 4.1 8.3C3.7134 8.3 3.4 8.6134 3.4 9C3.4 9.3866 3.7134 9.7 4.1 9.7ZM13.9 16C12.9817 16 12.2011 15.4106 11.9158 14.5895C11.8784 14.5967 11.8396 14.6 11.8 14.6H2.7C2.3134 14.6 2 14.2866 2 13.9C2 13.5134 2.3134 13.2 2.7 13.2H11.8C11.8396 13.2 11.8784 13.2033 11.9162 13.2096C12.2011 12.3894 12.9817 11.8 13.9 11.8C15.0598 11.8 16 12.7402 16 13.9C16 15.0598 15.0598 16 13.9 16ZM13.9 14.6C14.2866 14.6 14.6 14.2866 14.6 13.9C14.6 13.5134 14.2866 13.2 13.9 13.2C13.5134 13.2 13.2 13.5134 13.2 13.9C13.2 14.2866 13.5134 14.6 13.9 14.6Z" fill="#3585F5"/>
                    </svg>
                </button>
            </div>
        </div>




    </div>

</div>
