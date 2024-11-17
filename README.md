# Wifidog Auth (Laravel 11)

[![codecov](https://codecov.io/github/wifidog/wifidog-auth-laravel/graph/badge.svg?token=KXFrMQY7vo)](https://codecov.io/github/wifidog/wifidog-auth-laravel)
[![Docker Pulls](https://img.shields.io/docker/pulls/wifidog/wifidog-auth-laravel)](https://hub.docker.com/r/wifidog/wifidog-auth-laravel)
[![Laravel 11](https://github.com/wifidog/wifidog-auth-laravel/actions/workflows/laravel-11.yml/badge.svg)](https://github.com/wifidog/wifidog-auth-laravel/actions/workflows/laravel-11.yml)
[![LICENSE](https://img.shields.io/badge/license-Anti%20996-blue.svg)](https://github.com/wifidog/wifidog-auth-laravel/blob/master/LICENSE)

This project provides a auth server for wifidog. For API details, please see the [WiFiDog Protocol V1](https://github.com/wifidog/wifidog-auth-laravel/wiki).

## Features

- Pages
  - /login
  - /logout
  - /portal
  - /messages OR gw\_message.php
- Apis
  - /ping
  - /auth

## Getting Started

### Docker

```shell
docker run -p 8000:80 \
    --env APP_NAME="Wifidog Auth" \
    --env APP_ENV=local \
    --env APP_KEY=base64:silhtn4zkyodaaDIRSU0QEqq4CwKfjdzLqZectaHIi8= \
    --env DB_CONNECTION=sqlite \
    wifidog/wifidog-auth-laravel

open http://127.0.0.1:8000
```

```shell
docker run -p 8000:80 \
    --env APP_NAME="Wifidog Auth" \
    --env APP_ENV=local \
    --env APP_KEY=base64:silhtn4zkyodaaDIRSU0QEqq4CwKfjdzLqZectaHIi8= \
    --env DB_CONNECTION=mysql \
    --env DB_HOST=172.17.0.1 \
    --env DB_PORT=3306 \
    --env DB_DATABASE=wifidog \
    --env DB_USERNAME=root \
    --env DB_PASSWORD=1 \
    wifidog/wifidog-auth-laravel
```

### not Docker

```shell
composer install
cp .env.example .env
sudo chmod 777 bootstrap/cache
sudo chmod -R 777 storage
touch database/database.sqlite
chmod 777 database
chmod 666 database/database.sqlite
php artisan key:generate
php artisan migrate
./phpunit.sh

npm install
npm run dev
sudo ln -s `pwd` /var/www/
sudo ln -s `realpath config/apache2/sites-available/laravel.conf` /etc/apache2/sites-available/
sudo a2enmod rewrite
sudo a2ensite laravel && sudo service apache2 restart
echo '127.0.0.1 wifidog-auth.lan' | sudo tee -a /etc/hosts
curl 'http://wifidog-auth.lan/ping?gw_id=001217DA42D2&sys_uptime=742725&sys_memfree=2604&sys_load=0.03&wifidog_uptime=3861'
google-chrome http://wifidog-auth.lan/
```

If you want to use MySQL, change `.env` like this\(don't forget to [migrate](https://laravel.com/docs/migrations#running-migrations) again\):

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wifidog
DB_USERNAME=root
DB_PASSWORD=1
```

## Wifidog Config

If you want to use local computer as web server and your phone for auth test, you should login into your openwrt router, then add computer IP to `/etc/hosts`, and change `/etc/wifidog.conf`.

If your web server IP is 192.168.1.42, mac is 00:00:DE:AD:BE:AF, and your openwrt router IP is 192.168.1.1, operate like this:

```shell
ssh root@192.168.1.1
echo "192.168.1.42 wifidog-auth" >> /etc/hosts
/etc/init.d/dnsmasq restart
vi /etc/wifidog.conf
```

```txt
AuthServer {
    Hostname wifidog-auth.lan
    Path /
}
TrustedMACList 00:00:DE:AD:BE:AF
```

```shell
/etc/init.d/wifidog restart
sleep 10
/etc/init.d/wifidog status
```

Now take out your phone, connect the openwrt wifi, when you try to visit any http website, you will see this login page:

![phone screenshot of wifidog auth](https://user-images.githubusercontent.com/4971414/59157860-c1328180-8ae4-11e9-9325-51269f3c6c76.png)

After register or login, you can use internet.

### Social Login

If you want to use Facebook Login\([more providers are here](https://github.com/laravel-fans/laravel-ui-socialite)\), add these to `.env`

```ini
FACEBOOK_APP_ID=123456
FACEBOOK_APP_SECRET=secret
FACEBOOK_VALID_OAUTH_REDIRECT_URI="http://wifidog-auth.lan/login/facebook/callback"
AUTH_SOCIAL_LOGIN_PROVIDERS="Facebook"
```

then change ipset of router:

```shell
ssh root@192.168.1.1
opkg update
opkg install dnsmasq-full
echo "ipset create facebook hash:ip" >> /etc/firewall.user
echo "ipset=/facebook.com/fbcdn.net/facebook" >> /etc/dnsmasq.conf
fw3 reload
/etc/init.d/dnsmasq restart
```

Add this to `/etc/wifidog.conf`

```txt
FirewallRuleSet unknown-users {
    FirewallRule allow to-ipset facebook
}
```

If your router doesn't support ipset, can only add these to `/etc/wifidog.conf`, but this way can not guarantee the reliability.

```txt
FirewallRuleSet global {
    FirewallRule allow tcp to www.facebook.com
    FirewallRule allow tcp to m.facebook.com
    FirewallRule allow tcp to static.xx.fbcdn.net
}
```

```shell
/etc/init.d/wifidog restart
```

## Debug

If you have problems, try these methods:

1. debug auth server: check the access log and error log, they are usually in `/var/log/apache2/`
2. debug wifidog: edit `/usr/bin/wifidog-init`, change "start" section's "wifidog $OPTIONS" to "wifidog -f -d 9"

## Tech

- PHP Framework: [Laravel](https://laravel.com/)
- Coding standard: following [PSR-12](http://www.php-fig.org/psr/psr-12/). run `./lint.sh`
- Unit Test: using PHPUnit. run `php artisan test`
