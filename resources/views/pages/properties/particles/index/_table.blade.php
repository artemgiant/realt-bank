<table id="example" class="table table-hover">

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
                <p>Этаж</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper photo">
                <p>Фото</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper price">
                <p>Цена</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper contact">
                <p>Контакт</p>
            </div>
        </th>
    </tr>
    </thead>
    <tbody>
    @forelse($properties as $property)
        <tr>
            {{-- Checkbox --}}
            <td>
                <div class="tbody-wrapper checkBox">
                    <label class="my-custom-input">
                        <input type="checkbox" value="{{ $property->id }}">
                        <span class="my-custom-box"></span>
                    </label>
                </div>
            </td>

            {{-- Локация (пока пустое) --}}
            <td>
                <div class="tbody-wrapper location">
                    <span class="text-muted">-</span>
                </div>
            </td>

            {{-- Тип сделки --}}
            <td>
                <div class="tbody-wrapper type">
                    @if($property->dealType)
                        <p>{{ $property->dealType->name }}</p>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>
            </td>

            {{-- Площадь --}}
            <td>
                <div class="tbody-wrapper area">
                    @if($property->area_total)
                        <p>{{ $property->area_total }} м²</p>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>
            </td>

            {{-- Состояние --}}
            <td>
                <div class="tbody-wrapper condition">
                    @if($property->condition)
                        <p>{{ $property->condition->name }}</p>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>
            </td>

            {{-- Этаж --}}
            <td>
                <div class="tbody-wrapper floor">
                    @if($property->floor)
                        <p>{{ $property->floor }}@if($property->floors_total)/{{ $property->floors_total }}@endif</p>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>
            </td>

            {{-- Фото (пока пустое) --}}
            <td>
                <div class="tbody-wrapper photo">
                    <span class="text-muted">-</span>
                </div>
            </td>

            {{-- Цена --}}
            <td>
                <div class="tbody-wrapper price">
                    @if($property->price)
                        <p>{{ number_format($property->price, 0, '.', ' ') }} {{ $property->currency?->symbol ?? '$' }}</p>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>
            </td>

            {{-- Контакт (пока пустое) --}}
            <td>
                <div class="tbody-wrapper contact">
                    <span class="text-muted">-</span>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="9" class="text-center py-4">
                <div class="text-muted">
                    <p class="mb-2">Объекты не найдены</p>
                    <a href="{{ route('properties.create') }}" class="btn btn-primary">
                        Добавить первый объект
                    </a>
                </div>
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
