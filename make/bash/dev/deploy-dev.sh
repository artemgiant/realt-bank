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


#php artisan db:seed --class=CountrySeeder
#
#php artisan db:seed --class=DictionarySeeder --force



#ОЧИСТИТИ ДАНЫЕ
#php artisan tinker --execute="DB::table('property_contact')->truncate(); App\Models\Property\Property::query()->forceDelete(); App\Models\Contact\Contact::query()->delete();"
#СГЕНЕРИРОВАТЬ ДАННЫЕ
php artisan tinker --execute="App\Models\Property\Property::factory()->count(100)->withContacts(1)->create();"



#TRUNCATE TABLE states;

# Оновити Composer залежності
composer install --optimize-autoloader


# Очистити всі кеші
php artisan optimize:clear




#sudo supervisorctl reread
#sudo supervisorctl update
#sudo supervisorctl restart horizon
#sudo supervisorctl status
