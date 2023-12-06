# Laravel WebP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eyadhamza/laravel-webp.svg?style=flat-square)](https://packagist.org/packages/eyadhamza/laravel-webp)

[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/eyadhamza/laravel-webp/Check%20&%20fix%20styling?label=code%20style)](https://github.com/eyadhamza/laravel-webp/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/eyadhamza/laravel-webp.svg?style=flat-square)](https://packagist.org/packages/eyadhamza/laravel-webp)

---

This Laravel package is a simple wrapper for the [PHP Intervention Library](http://image.intervention.io/) to provide a more straightforward interface and convenient way to convert images to the WebP format - a next-generation image format - and resize them to render only the needed sizes.

## Installation

You can install the package via composer:

```bash
composer require eyadhamza/laravel-webp
```

Publish the config file with:

```bash
php artisan vendor:publish --provider="EyadHamza\LaravelWebp\LaravelWebpServiceProvider" --tag="webp-config"
```

The contents of the published config file:

```php
return [
    'quality' => 70,
    'height' => null,
    'width' => null,
    'overwrite' => true
];
```

## Usage

### Converting / Resizing Images in Eloquent Models:

To convert images in Eloquent Models, all you need to do is to add a cast to the image columns in your model:

```php
class TestModel extends Model
{
    protected $casts = [
        'image' => ToWebpCast::class . ':200,200,100',
        'avatar' => ToWebpCast::class,
    ];
}
```

The `ToWebpCast` class takes three optional parameters: width, height, and quality.
You can also pass the values in the config file as default values.
In the config file, set the `overwrite` value to `true` or `false`; if set to `true`, the old image will be deleted.

Now, whenever you set the image attribute,
it will be converted to WebP and resized to the specified dimensions—if given.

### Optimize Existing Images:

If you already have images on your local storage that are not optimized, and their paths are stored in the database, you can use the following Artisan command:

```bash
php artisan public:to-webp
```

To convert images in a specific directory:

```bash
php artisan public:to-webp --directory='public/images'
```

To delete old images, use the `--overwrite` option:

```bash
php artisan public:to-webp --overwrite
```

For optimizing files in static assets, use:

```bash
php artisan public:to-webp --assets
```

To modify the database attribute values to point to the new webp image, run:

```bash
php artisan images:to-webp Post
```

If you want more methods to conveniently change your image path, refer to the `ImageToWebpService` class.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Eyad Hamza](https://github.com/Eyadhamza)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
