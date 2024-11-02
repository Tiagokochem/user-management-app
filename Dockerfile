# Usa a imagem do PHP com Apache
FROM php:8.1-apache

# Instala extensões do MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia os arquivos da aplicação para o container
COPY ./src /var/www/html

# Define o diretório de uploads e ajusta permissões
RUN mkdir /var/www/html/uploads && chmod -R 777 /var/www/html/uploads
