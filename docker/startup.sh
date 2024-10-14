#!/usr/bin/env bash

# Place the environment variables in a script for CRON jobs to be able to access them.
declare -p | grep -Ev 'BASHOPTS|BASH_VERSINFO|EUID|PPID|SHELLOPTS|UID' > /container.env

/usr/sbin/crond
php-fpm -D
while ! nc -w 1 -z 127.0.0.1 9000; do sleep 0.1; done;
nginx
