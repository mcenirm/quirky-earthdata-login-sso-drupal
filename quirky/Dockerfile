FROM drupal:7-apache

RUN curl -sLR -o /usr/local/bin/drush \
        https://github.com/drush-ops/drush/releases/download/8.1.16/drush.phar \
    && chmod 0555 /usr/local/bin/drush

RUN apt-get update \
    && apt-get install -y \
        mariadb-client \
    && rm -rf /var/lib/apt/lists/*
