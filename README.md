# Symfony 5 REST API Todo example

### Getting Started

These instructions will get you a copy of the project up and running on your local machine 
for development and testing purposes. 

### Prerequisites

What things you need to install the software and how to install them.
- PHP 7.2.5+
- [composer](https://getcomposer.org/download/)
- [symfony](https://symfony.com/doc/current/setup.html)
- docker (optional)

### Installing

```bash
git clone https://github.com/artem-bazykin-ddi/symfony5-todo
cd symfony5-rest-api
cp .env.dist .env
## edit .env if needed
composer install
```

### Running the example

#### Install database
```bash
php bin/console doctrine:database:create
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

## API doc

You can check swagger on URL https://127.0.0.1:8000/api/doc
 
