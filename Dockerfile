FROM php:8.2-apache

# Cài đặt extension để PHP kết nối được MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Bật chế độ Rewrite của Apache (Để đường dẫn đẹp nếu web em có dùng)
RUN a2enmod rewrite

# Cho phép quyền ghi vào thư mục upload (nếu code em có up ảnh)
RUN chown -R www-data:www-data /var/www/html