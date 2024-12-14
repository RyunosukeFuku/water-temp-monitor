FROM php:7.4-apache

# 必要なパッケージをインストール
RUN docker-php-ext-install pdo pdo_mysql

# Apacheのドキュメントルートを設定（オプション）
COPY ./src /var/www/html
