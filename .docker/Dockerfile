ARG ROAD_RUNNER_IMAGE=2024.2.0
ARG DOLT_IMAGE=1.42.8

# Build dolt binary
FROM dolthub/dolt:$DOLT_IMAGE as dolt
# Build rr binary
FROM ghcr.io/roadrunner-server/roadrunner:$ROAD_RUNNER_IMAGE as rr
# Clone the project
FROM alpine/git as git

ARG REPOSITORY=https://github.com/llm-agents-php/sample-app.git
ARG BRANCH=main
RUN git clone -b $BRANCH $REPOSITORY /app

FROM php:8.3-cli-alpine3.18

RUN apk add --no-cache $PHPIZE_DEPS \
        curl \
        libcurl \
        wget \
        libzip-dev \
        libmcrypt-dev \
        libxslt-dev \
        libxml2-dev \
        openssl-dev \
        icu-dev \
        zip \
        unzip \
        linux-headers

RUN docker-php-ext-install \
        opcache \
        zip \
        dom \
        sockets

# PDO database drivers support
RUN docker-php-ext-install pdo_mysql

COPY --from=git /app /app
COPY --from=rr /usr/bin/rr /app
COPY --from=dolt /usr/local/bin/dolt /app
COPY --from=composer /usr/bin/composer /usr/bin/composer

ARG APP_VERSION=v1.0
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

RUN composer config --no-plugins allow-plugins.spiral/composer-publish-plugin false
RUN composer install --no-dev

WORKDIR /app

RUN mkdir .db
RUN ./dolt --data-dir=.db sql -q "create database llm;"

ENV APP_ENV=prod
ENV DEBUG=false
ENV VERBOSITY_LEVEL=verbose
ENV ENCRYPTER_KEY=def00000232ae92c8e8ec0699093fa06ce014cd48d39c3c62c279dd947db084e56ee48b5c91cebc1c5abe53f7755021d09043757561c244c1c0c765cfeb5db33eb45a903
ENV MONOLOG_DEFAULT_CHANNEL=roadrunner
ENV MONOLOG_DEFAULT_LEVEL=INFO
ENV APP_VERSION=$APP_VERSION
ENV RR_LOG_LEVEL=error

LABEL org.opencontainers.image.source=$REPOSITORY
LABEL org.opencontainers.image.description="LL Agents PHP"
LABEL org.opencontainers.image.licenses=MIT

CMD ./rr serve -c .rr.yaml
