# Users and groups management

Users and groups management site

## Setup

1. composer install
2. configure database in .env File
3. to create database run command: php bin/console doctrine:database:create
4. to make migrations run command: php bin/console make:migration
5. to migrate migrations run command: php bin/console doctrine:migrations:migrate
6. to load data fixtures run command: php bin/console doctrine:fixtures:load
7. to start server run command: php bin/console server:run

Now you can access site at `http://localhost:8000`

Admin user:

username: admin
password: password

## System requirements
PHP 7.2
MySQL 5.7


