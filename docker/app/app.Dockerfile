FROM php:8.2.2-fpm

LABEL maintainer="Habibur Rahman Riad <habiburrahman.cse11@gmail.com>"

# Setup docker arguments.
ARG TIMEZONE="Asia/Dhaka"

# App Root Path relative to context
ARG HOST_APP_ROOT_DIR="./codes/"
ARG WORK_DIR_PATH="/var/www/html"

# Setup some environment variables.
ENV TZ="${TIMEZONE}" \
    COMPOSER_VERSION="2.5.4" \
    COMPOSER_HOME="/usr/local/composer"

USER root

# Install & manage application dependencies
RUN echo "-- Configure Timezone --" \
        && echo "${TIMEZONE}" > /etc/timezone \
        && rm /etc/localtime \
        && dpkg-reconfigure -f noninteractive tzdata \
    && echo "-- Install/Upgrade APT Dependencies --" \
        && apt update \
        && apt upgrade -y \
        && apt install -V -y --no-install-recommends --no-install-suggests \
            bc \
            curl \
            openssl \
            unzip \
            zip \
            supervisor \
    && echo "-- Install Extra APT Dependencies --" \
        && if [ ! -z "${EXTRA_INSTALL_APT_PACKAGES}" ]; then \
            apt install -y ${EXTRA_INSTALL_APT_PACKAGES} \
        ;fi \
    && echo "-- Install PHP Extensions --" \
        && curl -L -o /usr/local/bin/install-php-extensions https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
        && chmod a+x /usr/local/bin/install-php-extensions \
        && sync \
        && install-php-extensions \
            bcmath \
            exif \
            fileinfo \
            gettext \
            intl \
            opcache \
            pcntl \
            pdo \
            pdo_mysql \
            redis \
            uuid \
            xml \
            zip \
      && echo "-- Install Extra PHP Extensions --" \
          && if [ ! -z "${EXTRA_INSTALL_PHP_EXTENSIONS}" ]; then \
              install-php-extensions ${EXTRA_INSTALL_PHP_EXTENSIONS} \
          ;fi \
    && echo "--- Clean Up ---" \
        && apt clean -y \
        && apt autoremove -y \
        && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# PHP Composer Installation & Directory Permissions
RUN curl -L -o /usr/local/bin/composer https://github.com/composer/composer/releases/download/${COMPOSER_VERSION}/composer.phar \
    && mkdir -p ${COMPOSER_HOME}/cache /tmp/xdebug/ \
    && chmod ugo+sw ${COMPOSER_HOME}/cache /tmp/xdebug/ \
    && mkdir /run/php \
    && chmod ugo+x /usr/local/bin/composer \
    && composer --version

ARG UID="1000"
ARG GID="1000"

RUN groupadd --gid ${GID} app \
    && useradd --uid ${UID} --create-home --system --comment "App User" --shell /bin/bash --gid app app \
    && chown -R app:app ${WORK_DIR_PATH} ${COMPOSER_HOME}

USER app

# Add our own Additional Entrypoints
COPY --chown=app:app ./docker/app/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR ${WORK_DIR_PATH}

COPY --chown=app:app ${HOST_APP_ROOT_DIR}composer*.json ${HOST_APP_ROOT_DIR}composer*.lock* /var/www/html/

# Installing the packages here to cache them
# So further installs from child images will
# download less / no dependencies.
RUN composer install --no-interaction --no-scripts --no-autoloader

# Copy Source Files
COPY --chown=app:app ${HOST_APP_ROOT_DIR}app ./app
COPY --chown=app:app ${HOST_APP_ROOT_DIR}bootstrap ./bootstrap
COPY --chown=app:app ${HOST_APP_ROOT_DIR}config ./config
COPY --chown=app:app ${HOST_APP_ROOT_DIR}database ./database
COPY --chown=app:app ${HOST_APP_ROOT_DIR}public ./public
COPY --chown=app:app ${HOST_APP_ROOT_DIR}resources ./resources
COPY --chown=app:app ${HOST_APP_ROOT_DIR}routes ./routes
COPY --chown=app:app ${HOST_APP_ROOT_DIR}storage ./storage
COPY --chown=app:app ${HOST_APP_ROOT_DIR}artisan ./

# Copy Supervisor configuration file
COPY --chown=app:app ./docker/app/supervisord.conf /etc/supervisor/supervisord.conf

# Composer Dump-Autoload
RUN composer install --no-dev --optimize-autoloader --classmap-authoritative

ENTRYPOINT [ "/usr/local/bin/docker-entrypoint.sh" ]

CMD [ "php-fpm" ]
