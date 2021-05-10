FROM pgroot/php:7.3.17

RUN  mkdir -p /app/log/supervisor \
    && touch /var/log/laravel.log \
    && chown -R www-data:www-data /var/log/laravel.log \
    && chmod 0777 /var/log/laravel.log

COPY . /var/www
COPY rancher/.env.example /var/www/.env
COPY ./public /var/www/html

# PHP 配置
COPY docker/upload.ini /usr/local/etc/php/conf.d/upload.ini

## Queue 配置
#COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
#COPY docker/supervisor-worker.conf /etc/supervisor/conf.d/worker.conf

# Schedule 配置
RUN apt-get update && apt-get -y install cron
#ADD rancher/crontabfile /etc/cron.d/hello-cron
#RUN chmod 0644 /etc/cron.d/hello-cron \
#    && touch /app/log/cron.log \
#    && crontab /etc/cron.d/hello-cron

## Apache 配置
#COPY docker/mpm_prefork.conf /etc/apache2/mods-available/mpm_prefork.conf
#RUN chmod 0644 /etc/apache2/mods-available/mpm_prefork.conf

WORKDIR /var/www

RUN chown -R www-data:www-data /var/www \
    && chmod -R 0777 /var/www/storage

RUN ["chmod", "+x", "./rancher/entrypoint.sh"]

EXPOSE 80
EXPOSE 443

LABEL "com.datadoghq.ad.logs"='[{"source": "99Plas_backend", "service": "99plas-api"}]'

ENTRYPOINT ["./rancher/entrypoint.sh"]