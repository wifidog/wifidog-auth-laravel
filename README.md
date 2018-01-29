# Wifidog Auth (Laravel 5.4)

This project provides a auth server for wifidog. For API details, please see the [WiFiDog Protocol V1](http://dev.wifidog.org/wiki/doc/developer/WiFiDogProtocol_V1).

## Pages

- login/
- portal/ 
- messages/ OR gw\_message.php

## Apis

- ping/
- auth/

## Getting Started

```
composer install
cp .env.example .env
sudo chmod 777 bootstrap/cache
sudo chmod -R 777 storage
touch database/database.sqlite
chmod 777 database
chmod 666 database/database.sqlite
php artisan key:generate
php artisan jwt:generate
php artisan migrate
sudo ln -s `pwd` /var/www/
sudo cp apache2/sites-enabled/* /etc/apache2/sites-enabled/
sudo service apache2 restart
echo "127.0.0.1 wifidog-auth.lan" | sudo tee -a /etc/hosts
curl 'http://wifidog-auth.lan/ping?gw_id=001217DA42D2&sys_uptime=742725&sys_memfree=2604&sys_load=0.03&wifidog_uptime=3861'
google-chrome http://wifidog-auth.lan/
```

If you want to use MySQL, change `.env` like this\(don't forget to [migrate](https://laravel.com/docs/5.4/migrations#running-migrations) again\):

```
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

```
ssh root@192.168.1.1
echo "192.168.1.42 wifidog-auth" >> /etc/hosts
/etc/init.d/dnsmasq restart
vi /etc/wifidog.conf
```

```
AuthServer {
    Hostname wifidog-auth.lan
    Path /
}
TrustedMACList 00:00:DE:AD:BE:AF
```

```
/etc/init.d/wifidog restart
sleep 10
/etc/init.d/wifidog status
```

Now take out your phone, connect the openwrt wifi, when you try to visit any http website, you will see this login page:

![phone screenshot of wifidog auth](https://user-images.githubusercontent.com/4971414/35483355-d5079ae2-047b-11e8-8e5a-eb79bf4ca205.png)

After register or login, you can use internet.

### Social Login

If you want to use Facebook Login, add these to `.env`

```
FACEBOOK_APP_ID=xxx
FACEBOOK_APP_SECRET=xxx
FACEBOOK_OAUTH_REDIRECT_URI="http://wifidog-auth.lan/login/facebook/callback"
SOCIAL_LOGIN_PROVIDERS="facebook"
```

then add these to router's `/etc/wifidog.conf`

```
FirewallRuleSet global {
    FirewallRule allow tcp to www.facebook.com
    FirewallRule allow tcp to m.facebook.com
    FirewallRule allow tcp to static.xx.fbcdn.net
}
```

```
/etc/init.d/wifidog restart
```

## Tech

- PHP Framework: [Laravel 5.4](https://laravel.com/docs/5.4/)
- Coding standard: following [PSR2](http://www.php-fig.org/psr/psr-2/). run `./lint.sh` to check.
- Unit Test: using PHPUnit. run `./phpunit.sh`
- CI: [![CircleCI](https://circleci.com/gh/sinkcup/wifidog-auth-laravel.svg?style=svg)](https://circleci.com/gh/sinkcup/wifidog-auth-laravel)
