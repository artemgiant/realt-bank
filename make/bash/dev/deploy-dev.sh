#!/bin/bash


# Скинути локальні зміни
git reset --hard && git clean -df

# Завантажити інформацію про віддалену гілку
git fetch origin

# Синхронізувати з віддаленою гілкою
git reset --hard origin/master



# Очистити кеш
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear


php artisan migrate


#php artisan currency:update-rates
#php artisan db:seed --class=CountrySeeder


#php artisan db:seed --class=DictionarySeeder --force



#ОЧИСТИТИ ДАНЫЕ
#php artisan tinker  tinker --execute="DB::table('property_translations')->truncate(); DB::table('property_features')->truncate(); DB::table('property_contact')->truncate(); App\Models\Property\Property::query()->forceDelete(); App\Models\Contact\Contact::query()->delete();"
#СГЕНЕРИРОВАТЬ ДАННЫЕ
#php artisan  tinker --execute="App\Models\Property\Property::factory()->count(100)->withContacts(1)->create();"


#php artisan  tinker --execute=" use App\Models\Reference\Developer; Developer::query()->update(['source' => 'import']); "

#Очистить и создать  девелопера
#php artisan tinker --execute="\App\Models\Reference\Developer::factory()->cleanAndCreate(100);"


#php artisan storage:link


#TRUNCATE TABLE states;

# Оновити Composer залежності
composer install --optimize-autoloader


# Очистити всі кеші
php artisan optimize:clear




#sudo supervisorctl reread
#sudo supervisorctl update
#sudo supervisorctl restart horizon
#sudo supervisorctl status
