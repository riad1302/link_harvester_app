FROM nginx:alpine

LABEL maintainer="Habibur Rahman Riad <habiburrahman.cse11@gmail.com>"

# Copy Nginx configuration
COPY ./docker/web/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/web/default.conf /etc/nginx/conf.d/default.conf
COPY ./docker/web/fastcgi-php.conf /etc/nginx/snippets/fastcgi-php.conf

# Copy application files
COPY ./codes/public /var/www/html/public
