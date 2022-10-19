# Install Laravel packages
composer install

if [ ! -e ./.env ]; then
  cp .env.example .env
fi

php artisan serve --host=0.0.0.0
