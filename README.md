# Workshops rota
PHP application that displays staff rota shifts data. It's PHP application based on Symfony 3.4 framework.

## Requirement:

PHP 7, composer

## Installation

- `git clone https://github.com/yakobabada/workshops.git`
- `cd workshops/`
- `composer install`
- `bin/console doctrine:database:create`
- `bin/console doctrine:migration:migrate`

## Browsing the rota with id `332`

- On a browser type this url `http://127.0.0.1:8000/332`

## Running the test
- `./vendor/bin/simple-phpunit`

