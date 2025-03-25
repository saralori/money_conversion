#!/bin/bash

chown -R www-data:www-data /var/www/html

php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

exec "$@"