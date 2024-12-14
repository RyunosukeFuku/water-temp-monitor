FROM php:8.1-apache

# システムの更新と必要なパッケージのインストール
RUN apt-get update && apt-get install -y \
    curl \
    unzip

# pdo_mysql拡張をインストール
RUN docker-php-ext-install pdo pdo_mysql

# 必要なPHP設定
RUN echo "allow_url_fopen = On" > /usr/local/etc/php/conf.d/custom.ini

# curl拡張のインストール
RUN docker-php-ext-install curl

# Composerのインストール
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# 作業ディレクトリの設定
WORKDIR /var/www/html

# Composerファイルのコピー
COPY composer.json composer.lock ./

# 依存関係のインストール
RUN composer install --no-scripts --no-autoloader

# ソースコードのコピー
COPY . .

# オートローダーの生成
RUN composer dump-autoload --optimize