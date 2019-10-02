# MedKit

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

MedKit is a toolbox used by Mediactive Digital.

## Installation

Create a new project, then require medKit
``` bash
$ laravel new projectZero #version 6.0.4 
$ cd projectZero/ 
$ composer require mediactive-digital/medkit
$ php artisan medkit:install
```
> update your .env, create your bdd

## Run the migrations (with docker)

Update your .env for access between docker containers : 
```dotenv
DB_CONNECTION=mysql
DB_HOST=db_mysql_$DB_DATABASE
DB_PORT=3306
DB_DATABASE=$DB_DATABASE
DB_USERNAME=root
DB_PASSWORD=toor
```
> Replace $DB_DATABASE by your db name

Install docker comunity edition, then run
```bash
$ docker-compose up
``` 
> configurations are in docker-compose.yaml

Then run the wizzard inside Docker.
```bash
$ docker-compose exec core_services php /var/www/artisan medkit:migrate
```

## Run the migrations (with your own services)

``` bash
$ php artisan medkit:migrate
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [author name][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/mediactivedigital/medkit.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/mediactivedigital/medkit.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/mediactivedigital/medkit/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/mediactivedigital/medkit
[link-downloads]: https://packagist.org/packages/mediactivedigital/medkit
[link-travis]: https://travis-ci.org/mediactivedigital/medkit
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/mediactivedigital
[link-contributors]: ../../contributors
