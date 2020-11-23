# Symfony 5 REST API Todo example

### Getting Started

These instructions will get you a copy of the project up and running on your local machine 
for development and testing purposes. 

### Prerequisites

What things you need to install the software and how to install them.
- PHP 7.4+
- [composer](https://getcomposer.org/download/)
- [symfony](https://symfony.com/doc/current/setup.html)
- [docker](https://docs.docker.com/compose/install/)

### Installing

```bash
cp .env.dist .env
## edit .env if needed
composer install
```

### Running the example

#### Apply migrations to DB

```bash
php bin/console doctrine:migrations:migrate
```

#### Run symfony

```bash
symfony server:start 
```
or
```bash
composer require symfony/web-server-bundle --dev
php bin/console server:start
```

### API doc

You can check swagger on URL https://127.0.0.1:8000/api/doc

### Running tests

```bash
./bin/phpunit
```
 
