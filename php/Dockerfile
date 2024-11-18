# 使用 PHP 8.2 FPM 镜像作为基础
FROM php:8.2-fpm

# 安装必要的 PHP 扩展和依赖
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-install -j$(nproc) mysqli pdo_mysql

# 设置工作目录
WORKDIR /var/www/html
# 确保 PHP-FPM 能够写入
RUN chown -R www-data:www-data /var/www/html

# 暴露端口并启动 PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]

