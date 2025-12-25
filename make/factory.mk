factory-properties:
	./vendor/bin/sail tinker --execute="App\Models\Property\Property::factory()->count(50)->withContacts(1)->create();"

# Создать 100 объектов с контактами
factory-properties-100:
	./vendor/bin/sail tinker --execute="App\Models\Property\Property::factory()->count(100)->withContacts(1)->create();"

# Создать 50 контактов с телефонами
factory-contacts:
	./vendor/bin/sail tinker --execute="App\Models\Contact\Contact::factory()->count(50)->withPhones(2)->create();"

# Очистить properties и связанные контакты
factory-clean:
	./vendor/bin/sail tinker --execute="DB::table('property_contact')->truncate(); App\Models\Property\Property::query()->forceDelete(); App\Models\Contact\Contact::query()->delete();"
