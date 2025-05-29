# Dockerfile

FROM php:8.2-fpm

# 1) Оновлюємо пакети й встановлюємо залежності
RUN apt-get update && \
    apt-get install -y \
      git \
      curl \
      zip \
      unzip \
      libpq-dev \
      libonig-dev \
      libzip-dev && \
    docker-php-ext-install pdo_pgsql mbstring zip

# 2) Копіюємо Composer із офіційного образу
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 3) Встановлюємо робочу директорію
WORKDIR /var/www

# 4) Запускаємо PHP‑FPM
CMD ["php-fpm"]