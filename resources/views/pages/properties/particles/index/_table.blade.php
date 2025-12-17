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
            <td>{{ $property->id }}</td>
            <td><span class="text-muted">-</span></td>
            <td>
                @if($property->dealType)
                    <span class="badge bg-primary">{{ $property->dealType->name }}</span>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>

            <td>
                @if($property->area_total)
                    {{ $property->area_total }} м²
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>

            <td>
                @if($property->condition_id)
                    {{ $property->condition->name }}
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>


            {{--                    floor--}}

            <td>
                @if($property->floor)
                    {{ $property->floor }}
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>

            <td>
                <span class="text-muted">-</span>
            </td>


            <td>
                @if($property->price)
                    {{ number_format($property->price, 0, '.', ' ') }}
                    {{ $property->currency?->symbol ?? '$' }}
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>

            <td>
                <span class="text-muted">-</span>
            </td>
            <td>
                {{ $property->created_at->format('d.m.Y') }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center py-4">
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
