#!/usr/bin/env bash

# PHP FPM ENTRYPOINT CONFIGURATION
# Run our defined exec if args empty
if [ -z "$1" ]; then
    role=${CONTAINER_ROLE:-app}
    env=${APP_ENV:-production}

    echo "Role ::> $role"
    echo "App Env ::> $env"

   if [ "$env" != "local" ]; then

       echo "Caching configuration..."
       (cd /var/www/html && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear)
       (cd /var/www/html && php artisan config:cache && php artisan event:cache && php artisan route:cache && php artisan view:cache)

   fi

    if [ "$role" = "app" ]; then

        echo "Running PHP-FPM via Supervisor..."
        exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
        # exec php-fpm

    elif [ "$role" = "queue" ]; then

        echo "Running Queue via Supervisor in '${QUEUE_RUN_MODE}'..."
        exec php /var/www/html/artisan queue:work -vv --no-interaction --tries=3 --sleep=5 --timeout=300 --delay=10 --backoff=30,60

    else
        echo "Could not match the container role \"$role\""
        exit 1
    fi

else
    exec "$@"
fi
