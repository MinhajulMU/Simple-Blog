# Simple Blog Services
Simple Api Services for Blog
- PHP 8.3.10
- Mysql  Ver 8.4.2
- Laravel 11.23.5
- Sanctum Authentication
- Mail Service
- PHPUnit

## Installation
```
git clone git@github.com:MinhajulMU/Simple-Blog.git
```
```
cd Simple-Blog
```
```
composer install
```
```
Create two local database. one for the app database and one for the testing database.
for example:
- simple_blog
- simple_blog_test
```
```
- Setup environment
  - cp .env.example .env
  - cp .env.example .env.testing
- setup the database connection for each environment
    - variable to setup:
        - DB_DATABASE=
        - DB_USERNAME=
        - DB_PASSWORD=
- setup the smtp mail configuration for each environment
    - variable to setup
        - MAIL_MAILER=
        - MAIL_HOST=
        - MAIL_PORT=
        - MAIL_USERNAME=
        - MAIL_PASSWORD=
        - MAIL_ENCRYPTION=
        - MAIL_FROM_ADDRESS=
        - MAIL_FROM_NAME=

    you can configure with your mail server or using  https://mailtrap.io for testing
```
```
run php artisan migrate
```
```
run php artisan DB:seed
```
```
run php artisan serve to start
```

## API Documentation
```
https://documenter.getpostman.com/view/5148780/2sAXqs838U
```

## QUEUE
```
run php artisan queue:work (to execute the queue job)
```
```
run php artisan app:job-trigger {email} (to dispatch queue job, but currently only available for send welcome email job)
```

## Testing
```
php artisan test ( to see the unit testing result)
vendor/bin/phpunit --coverage-html reports/ ( to see the current unit testing coverage)
```

