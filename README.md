# Laravel webp

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eyadhamza/laravel-webp.svg?style=flat-square)](https://packagist.org/packages/eyadhamza/laravel-webp)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/eyadhamza/laravel-webp/run-tests?label=tests)](https://github.com/eyadhamza/laravel-webp/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/eyadhamza/laravel-webp/Check%20&%20fix%20styling?label=code%20style)](https://github.com/eyadhamza/laravel-webp/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/eyadhamza/laravel-webp.svg?style=flat-square)](https://packagist.org/packages/eyadhamza/laravel-webp)

---
This package is a simple wrapper for [PHP Intervention Library]() to provide a more 
simple interface and convenient way to convert images to webp - next generation format - extension, and resize them to render only needed sizes.


## Installation

You can install the package via composer:

```bash
composer require eyadhamza/laravel-webp
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="EyadHamza\LaravelWebp\LaravelWebpServiceProvider" --tag="laravel-webp-config"
```

This is the contents of the published config file:

the values represent the default values that will be used to convert the image, 
you can set the values of height and width to the values you would like to have in the converted image.

```php
return [
    'quality' => 70,
    'height' => null,
    'width' => null
];
```

## Usage

### Converting Images:
To use the package on a given Eloquent Model you should use HandleWebpConversion Trait
```php
class TestModel extends Model
{
    use HandleWebpConversion;
}
```
The Package will look for a protected property named $imageField that 
will be used by default to change the attribute value in the database when saving the model.
```php
class TestModel extends Model
{
    use HandleWebpConversion;
    
    protected string $imageField = 'image';
}
```

Now after you prepared your model, you can call the saveImageAsWebp() method on any model object that has the url of the image.
```php
$testModel->saveImageAsWebp();
```
The saveImageAsWebp() method will do the following:

1- Convert The image on disk to webp

2- keep the old image

3- change the imageField value in the database to point to the new image path with the new extension

4- Create a log entry in the laravel.log file with the details of the operation
and the before and after size e.g:
```php
Image: public/test.jpg Before: 0.3126 after: 0.0460 Percentage: 85.27
```

---
What if you want to delete the old image?

```php
$testModel->overwriteImageAsWebp();
```
The overwriteImageAsWebp() method will do just like the previous, but it will delete the old image from disk

--- 
### Resizing Images
If you tried Lighthouse to test your application, you may have got a suggestion that the rendered image is actually smaller than 
the image sent by the server.

A solution to that will be using a controller that changes the size on demand.

But that solution is not the best solution as image processing is an intensive operation for the CPU resources, you don't
want to have your page resize everytime your page reloads.

So, a better approach is to do that task one time and save another resized image on disk.

And It's Simple Enough!

Let's say that you want to resize the image to 400x400

```php
$testModel->resizeImage(400, 400);
```

The resizeImage($width, $height) method will do the following:

1- Save a version of the image on disk to webp with the new dimension with a new name that ends with _400x400.webp

2- Return the Full Path of the image, so you can use it somewhere in your views.

### Optimize Existing Images:
What if you already have images on your local storage that are not optimized, 
and surely those images' path are also stored in the database.

It will be kinda hard to do that manually and convert each one by hand!

The Solution is Here as an Artisan Command:

First we will modify files on disk then we will run another command to modify the database attribute to point to the new location
```bash
php artisan public:to-webp
```
This will convert images in the public directory, you can specify a file using the directory option 
```bash
php artisan public:to-webp directory = 'public/images'
```
Note that the command will keep the old images, if you want to delete the old images you can pass --overwrite to the command
```bash
php artisan public:to-webp --overwrite
```
If you would like to optimize the files in your static assets you can pass --assets

```bash
php artisan public:to-webp --assets
```

Now to modify the database attribute value to point to the new webp image we run 
the command, and we pass the name of the Eloquent model like this - this assumes that you have your models under App\Models - 
alternatively you may pass the full class name.
```bash
php artisan images:to-webp Post
```
The previous command will look for the protected property $imageField in your model.

Alternatively, you can pass the name of the attribute as will.
```bash
php artisan images:to-webp User avatar
```

If you want to use more methods that conveniently change your image path you may refer to the ImageToWebpService class

You can also call the methods directly using the Facade ImageToWebp
## Testing
``` Warning: Tests Are Potentially Destructive```
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
