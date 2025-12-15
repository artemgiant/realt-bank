<div class="my-dropdown">
    <div class="my-dropdown-input-wrapper">
        <button class="my-dropdown-geo-btn" type="button" data-bs-toggle="modal" data-bs-target="#geoModal">
            <picture>
                <source srcset="{{ asset('img/icon/geo.svg') }}" type="image/webp">
                <img src="{{ asset('img/icon/geo.svg') }}" alt="">
            </picture>
        </button>
        
        <label class="my-dropdown-label">
            <input class="my-dropdown-input" name="location" type="text" autocomplete="off" placeholder="Введите название">
        </label>

        <button class="my-dropdown-btn arrow-down" id="btn-open-menu" type="button">
            <picture>
                <source srcset="{{ asset('img/icon/arrow-right-white.svg') }}" type="image/webp">
                <img src="{{ asset('img/icon/arrow-right-white.svg') }}" alt="">
            </picture>
        </button>
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
                </div>
            </div>
        </div>
    </div>
</div>
