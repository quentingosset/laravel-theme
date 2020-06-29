
![PHP Composer](https://github.com/quentingosset/laravel-theme/workflows/PHP%20Composer/badge.svg)
| **Laravel**  |  **laravel-themes** |
|---|---|
| 5.4  | ^1.0  |
| 5.5  | ^2.0  |
| 5.6  | ^3.0  |
| 5.7  | ^4.0  |
| 5.8  | ^5.0  |
| 6.0  | ^6.0  |
| 7.0  | ^7.0 |

`nwidart/laravel-themes` is a Laravel package which created to manage your large Laravel app using themes. Theme is like a Laravel package, it has some views, controllers or models. This package is supported and tested in Laravel 7.

This package is a re-published, re-organised and maintained version of [pingpong/themes](https://github.com/pingpong-labs/themes), which isn't maintained anymore. This package is used in [AsgardCMS](https://asgardcms.com/).

With one big added bonus that the original package didn't have: **tests**.

Find out why you should use this package in the article: [Writing modular applications with laravel-themes](https://nicolaswidart.com/blog/writing-modular-applications-with-laravel-themes).

## Install

To install through Composer, by run the following command:

``` bash
composer require nwidart/laravel-themes
```

The package will automatically register a service provider and alias.

Optionally, publish the package's configuration file by running:

``` bash
php artisan vendor:publish --provider="Nwidart\Themes\LaravelThemesServiceProvider"
```

### Autoloading

By default the theme classes are not loaded automatically. You can autoload your themes using `psr-4`. For example:

``` json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Themes\\": "Themes/"
    }
  }
}
```

**Tip: don't forget to run `composer dump-autoload` afterwards.**

## Documentation

You'll find installation instructions and full documentation on [https://nwidart.com/laravel-themes/](https://nwidart.com/laravel-themes/).

## Credits

- [Nicolas Widart](https://github.com/nwidart)
- [gravitano](https://github.com/gravitano)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
