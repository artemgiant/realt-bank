# Factory команды для Property

# Создать 50 объектов
factory-properties:
	./vendor/bin/sail tinker --execute="App\Models\Property\Property::factory()->count(50)->create();"

# Создать 100 объектов
factory-properties-100:
	./vendor/bin/sail tinker --execute="App\Models\Property\Property::factory()->count(100)->create();"

# Очистить properties
factory-clean:
	./vendor/bin/sail tinker --execute="App\Models\Property\Property::query()->forceDelete();"
