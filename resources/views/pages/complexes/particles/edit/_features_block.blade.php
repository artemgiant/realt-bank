{{-- Особенности (features) - мульти-селект с тегами --}}
@php
    $selectedFeatures = old('features', $complex->features ?? []);
    if (is_string($selectedFeatures)) {
        $selectedFeatures = json_decode($selectedFeatures, true) ?? [];
    }
@endphp
<div class="item w15">
    <span class="item-label">Особенности</span>
    <div class="multiple-menu" id="features-menu">
        <button type="button" class="multiple-menu-btn" data-open-menu="false">
            Выберите параметры
        </button>
        <div class="multiple-menu-wrapper">
            <label>
                <input class="multiple-menu-search" autocomplete="off" type="text" placeholder="">
            </label>
            <ul class="multiple-menu-list">
                @foreach($features as $feature)
                    <li class="multiple-menu-item">
                        <label class="my-custom-input">
                            <input type="checkbox"
                                   name="features[]"
                                   value="{{ $feature->id }}"
                                    {{ in_array($feature->id, $selectedFeatures) ? 'checked' : '' }}>
                            <span class="my-custom-box"></span>
                            <span class="my-custom-text">{{ $feature->name }}</span>
                        </label>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
