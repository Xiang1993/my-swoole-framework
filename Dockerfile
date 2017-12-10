FROM php:7.0-cli

# 更换镜像源
RUN sed -i 's/deb.debian.org/mirrors.aliyun.com/g' /etc/apt/sources.list

RUN apt-get update && apt-get install -y \
	 vim \
    && docker-php-ext-install -j$(nproc) iconv pdo pdo_mysql \
    && pecl install swoole \
    && docker-php-ext-enable swoole

WORKDIR /usr/src/myapp

COPY . .

EXPOSE 9501

CMD [ "php", "index.php" ]