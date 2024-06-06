#!/bin/bash

# Run migrations
#php artisan migrate --force

# Start Supervisor
exec supervisord -c /etc/supervisor/supervisord.conf
