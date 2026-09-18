# Build stage: PHP and Node together, vite-plugin-tempest calls `php tempest vite:config`
FROM dunglas/frankenphp:1-php8.5 AS build

RUN install-php-extensions gd intl
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=node:22-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

WORKDIR /app
ENV ENVIRONMENT=production DISCOVERY_CACHE=true

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist
COPY package.json package-lock.json ./
RUN npm ci

COPY . .
# dump-autoload runs discovery:generate through post-autoload-dump
RUN composer dump-autoload --optimize --no-dev \
 && npm run build \
 && php tempest media:build \
 && rm -rf node_modules

# Runtime stage: FrankenPHP serves /app/public, Coolify's proxy handles TLS
FROM dunglas/frankenphp:1-php8.5

RUN install-php-extensions gd intl opcache
WORKDIR /app
COPY --from=build /app /app

ENV SERVER_NAME=:80 ENVIRONMENT=production DISCOVERY_CACHE=true INTERNAL_CACHES=true
EXPOSE 80
