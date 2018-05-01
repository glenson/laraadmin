#!/bin/bash
cd ~/projects
rm -rf laraadmin
sudo -H -u postgres psql -c "DROP DATABASE laraadmin"
sudo -H -u postgres psql -c "DROP USER laraadmin"
sudo -H -u postgres psql -c "CREATE USER laraadmin WITH PASSWORD 'laraadmin'"
sudo -H -u postgres psql -c "CREATE DATABASE laraadmin OWNER laraadmin"
laravel new laraadmin
cd laraadmin
cat .env.example | sed -e "s/mysql/pgsql/" | sed -e "s/3306/5432/"  \
| sed -e "s/homestead/laraadmin/" | sed -e "s/secret/laraadmin/" > .env
chmod -R 770 storage/ bootstrap/ database/migrations/
composer require spatie/laravel-backup
composer require dwij/laraadmin
cd vendor/dwij
mv laraadmin laraadmin.bak
git clone https://github.com/glenson/laraadmin.git laraadmin
cat ~/projects/laraadmin/config/app.php | \
	sed -e ':a' -e 'N' -e '$!ba' -e 's/App\\Providers\\RouteServiceProvider::class,/App\\Providers\\RouteServiceProvider::class,\n\tDwij\\Laraadmin\\LAProvider::class,\n/' > ~/projects/laraadmin/config/app.php.tmp
mv ~/projects/laraadmin/config/app.php.tmp ~/projects/laraadmin/config/app.php
cd ~/projects/laraadmin
php artisan la:install
php artisan key:generate