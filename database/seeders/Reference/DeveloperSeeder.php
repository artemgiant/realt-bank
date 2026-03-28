<?php

namespace Database\Seeders\Reference;

use App\Models\Location\City;
use App\Models\Location\State;
use App\Models\Reference\Block;
use App\Models\Reference\Complex;
use App\Models\Reference\Developer;
use App\Models\Reference\DeveloperLocation;
use App\Models\Reference\Dictionary;
use Illuminate\Database\Seeder;

class DeveloperSeeder extends Seeder
{
    /**
     * ID Одесской области
     */
    private const ODESSA_STATE_ID = 14;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $state = State::find(self::ODESSA_STATE_ID);

        // Справочники
        $categoryId = Dictionary::where('type', Dictionary::TYPE_COMPLEX_CATEGORY)
            ->where('name', 'Жилой комплекс')->value('id');

        $objectTypeId = Dictionary::where('type', Dictionary::TYPE_PROPERTY_TYPE)
            ->where('name', 'Квартира')->value('id');

        $conditionId = Dictionary::where('type', Dictionary::TYPE_CONDITION)
            ->where('name', 'От строителей')->value('id');

        $wallTypeId = Dictionary::where('type', Dictionary::TYPE_WALL_TYPE)
            ->where('name', 'Газоблок')->value('id');

        $featureIds = Dictionary::where('type', Dictionary::TYPE_COMPLEX_FEATURE)
            ->whereIn('name', [
                'Возле супермаркета',
                'Парковая зона',
                'Гостевой паркинг',
                'Возле школы',
                'Возле детсада',
            ])
            ->pluck('id')
            ->toArray();

        // Обновляем всех существующих девелоперов
        $developers = Developer::all();

        foreach ($developers as $developer) {
            // Локация: Одесская обл
            if ($state) {
                DeveloperLocation::updateOrCreate(
                    [
                        'developer_id' => $developer->id,
                        'location_type' => 'state',
                        'location_id' => $state->id,
                    ],
                    [
                        'location_name' => $state->name,
                        'full_location_name' => $state->name,
                    ]
                );
            }

            // Обновляем комплексы девелопера
            $developer->complexes()->each(function (Complex $complex) use (
                $categoryId, $objectTypeId, $conditionId, $featureIds, $wallTypeId
            ) {
                $stateId = $complex->city_id
                    ? City::where('id', $complex->city_id)->value('state_id')
                    : null;

                $complex->update([
                    'state_id' => $stateId,
                    'categories' => $categoryId ? [$categoryId] : [],
                    'object_types' => $objectTypeId ? [$objectTypeId] : [],
                    'conditions' => $conditionId ? [$conditionId] : [],
                    'features' => $featureIds,
                ]);

                // Обновляем блоки комплекса — тип стен
                if ($wallTypeId) {
                    Block::where('complex_id', $complex->id)
                        ->update(['wall_type_id' => $wallTypeId]);
                }
            });
        }
    }
}
