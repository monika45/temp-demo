#!/bin/bash

php artisan config:cache

chown -R www-data:www-data /app/log/supervisor

env=${APP_ENV}
role=${CONTAINER_ROLE}
enable_queue=${ENABLE_QUEUE}

# 根据分支复制相应配置
if [ "$env" == "production" ]; then
    echo '设置生成环境 Apache 配置'
else
    echo '设置非生产环境 Apache 配置'
    # Apache 配置
    cp -rf /var/www/docker/mpm_prefork.conf /etc/apache2/mods-available/mpm_prefork.conf
    chmod -R 0644 /etc/apache2/mods-available/mpm_prefork.conf
fi

cp -rf /var/www/docker/000-default.conf /etc/apache2/sites-available/000-default.conf
chmod -R 0644 /etc/apache2/sites-available/000-default.conf

echo '启动 apache 服务'
service apache2 start

## 根据容器配置并启动对应服务
#if [ "$role" == "queue_schedule" ]; then
#    echo '设置 supervisor 配置'
#    # Schedule 配置
#    cp -rf /var/www/docker/supervisord.conf /etc/supervisor/supervisord.conf
#    cp -rf /var/www/docker/supervisor-worker.conf /etc/supervisor/conf.d/worker.conf
#
#    echo '启动 supervisor 服务'
#    service supervisor start
#
#    echo '设置 cron 配置'
#    # Queue 配置
#    cp -rf /var/www/rancher/crontabfile /etc/cron.d/hello-cron
#    chmod -R 0644 /etc/cron.d/hello-cron \
#            && touch /app/log/cron.log \
#            && crontab /etc/cron.d/hello-cron
#
#    echo '启动 cron 服务'
#    service cron start
#fi

tail -f /var/log/laravel.log
