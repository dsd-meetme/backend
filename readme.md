## Meetme backend
Build status: [![Build Status](https://travis-ci.org/dsd-meetme/backend.svg?branch=master)](https://travis-ci.org/dsd-meetme/backend) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dsd-meetme/backend/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dsd-meetme/backend/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/dsd-meetme/backend/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/dsd-meetme/backend/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/dsd-meetme/backend/badges/build.png?b=master)](https://scrutinizer-ci.com/g/dsd-meetme/backend/build-status/master) [![Latest Stable Version](https://poser.pugx.org/dsd-meetme/backend/v/stable)](https://packagist.org/packages/dsd-meetme/backend) [![Total Downloads](https://poser.pugx.org/dsd-meetme/backend/downloads)](https://packagist.org/packages/dsd-meetme/backend) [![Latest Unstable Version](https://poser.pugx.org/dsd-meetme/backend/v/unstable)](https://packagist.org/packages/dsd-meetme/backend) [![License](https://poser.pugx.org/dsd-meetme/backend/license)](https://packagist.org/packages/dsd-meetme/backend)


This application uses laravel 5.1.* (a php framework)

Api example web server: http://api.plunner.com. It is just an example, so we don't guarantee that everything works

# How to install

1. Clone repository
1. Install dependencies with composer `composer install` (http://getcomposer.org)

or simply

`composer create-project dsd-meetme/backend` (this gets the last stable version)

# How to configure

1. Create database
1. Configure database data in .env file
1. Configure private keys in .env file
    1. `JWT_SECRET` via `php artisan jwt:generate`
    1. `APP_KEY` via `php artisan key:generate `
1. perform `php artisan migrate`
1. configure urls in `config/app.php` (this only for real environment)
1. this must be installed on the root of the virtual host
1. configure crontab `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1` to optimise and caldav import
1. install `GLPSOL` `https://en.wikibooks.org/wiki/GLPK/Linux_packages` (needed for optimisation task -> finding perfect meeting slot)
1. configure additional things like emails, optimisation and so in the config files

# How to develop
Use phpstorm is one of the best solution, since it has laravel plugin.

After cloning and installing repository:

1. Run `php artisan ide-helper:generate` and `php artisan ide-helper:meta`, so you create meta files for autocomplete in the IDE.
1. Run `php artisan ide-helper:models` each time models are updated (not in other case) and only if models are updated.
1. Install [laravel plugin](https://github.com/Haehnchen/idea-php-laravel-plugin)
 
# How to test with phpstorm
* Use phunit 4.* not 5
* execute as test phpunit.xml

# Notes
 * You should insert your name as author in composer file
 * We use UTC time
 * In real environment you should use apache2
 * To not to perform tests of console tasks, since they can be have problems on windows and they need specif software, set the following env variable `DO_CONSOLE_TESTS=false`
 * exec calls must be enabled in php-cli
 * tmp dir permissions needed
 * The library is tested only on linux, we don't know the behaviour of critical parts (optimisation and caldav sync) on other systems
 * Details about optimisation task [https://docs.google.com/document/d/18vCFEVrd8ENgS80hC-ACjSicDFYXV2QjoFiO3FiGZ5w/edit](https://docs.google.com/document/d/18vCFEVrd8ENgS80hC-ACjSicDFYXV2QjoFiO3FiGZ5w/edit)


# Credits
* [laravel framework](http://laravel.com/)
* [laravel ide helper](https://github.com/barryvdh/laravel-ide-helper)
* [laravel phpstorm plugin](https://github.com/Haehnchen/idea-php-laravel-plugin)
* [laravel noredirect traits for JWT](https://github.com/thecsea/jwt-auth)
* [laravel cors library](https://github.com/barryvdh/laravel-cors)
* [caldav client adapter library](https://github.com/thecsea/caldav-client-adapter)
* [GLPK](https://www.gnu.org/software/glpk/)
* [mailgun](http://www.mailgun.com/)
