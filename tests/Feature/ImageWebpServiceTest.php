<?php

use EyadHamza\LaravelWebp\Exceptions\NoImageGivenException;
use EyadHamza\LaravelWebp\Exceptions\NotImageException;
use EyadHamza\LaravelWebp\ImageToWebp;

use EyadHamza\LaravelWebp\Tests\TestSupport\Models\TestModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\withoutExceptionHandling;

beforeEach(function () {
    Storage::persistentFake();

    $this->image = UploadedFile::fake()
        ->image('image_one.jpg')
        ->store('public');

    $this->webpImage = Str::replace('.jpg', '.webp', $this->image);

    TestModel::factory()->create([
        'image' => $this->image,
        'avatar' => $this->image,
    ]);
});

it('can save an image', function () {
    $testImage = TestModel::find(1);

    $testImage->saveImageAsWebp();

    Storage::disk()
        ->assertExists($this->webpImage);

    Storage::disk()
        ->assertExists(ImageToWebp::getOldImageRelativePath());
});
it('can save an image with a very small size', function () {
    $testImage = TestModel::find(1);

    $testImage->saveImageAsWebp();

    Storage::disk()
        ->assertExists($this->webpImage);

    Storage::disk()
        ->assertExists(ImageToWebp::getOldImageRelativePath());
});

it('can save an image with the right name', function () {
    $testImage = TestModel::find(1);

    $testImage->saveImageAsWebp();

    Storage::disk()
        ->assertExists($this->webpImage);

    Storage::disk()
        ->assertExists($this->webpImage);

    assertDatabaseHas('test_images', [
        'image' => $this->webpImage,
    ]);
});

it('can overwrite an image', function () {
    withoutExceptionHandling();
    $testImage = TestModel::find(1);

    $testImage->overwriteImageAsWebp();

    Storage::disk()
        ->assertExists($this->webpImage);

    Storage::disk()
        ->assertMissing(ImageToWebp::getOldImageRelativePath());
});

it('must modify image url in the database', function () {
    $testImage = TestModel::find(1);

    $testImage->saveImageAsWebp();

    assertDatabaseHas('test_images', [
        'image' => $this->webpImage,
    ]);
});

it('can  save an image by passing the path', function () {
    $testImage = TestModel::find(1);

    ImageToWebp::make($testImage->image)->save();

    Storage::disk()
        ->assertExists(ImageToWebp::getOldImageRelativePath());
});

it('can overwrite an image by passing the path', function () {
    $testImage = TestModel::find(1);

    ImageToWebp::make($testImage->image)->overwrite();

    Storage::disk()
        ->assertExists(ImageToWebp::getWebpRelativePath(ImageToWebp::toRelativePath($testImage->image)));

    Storage::disk()
        ->assertMissing(ImageToWebp::getOldImageRelativePath());
});

it('must modify image url in the database by passing the path', function () {
    $testImage = TestModel::find(1);

    $testImage->saveImageAsWebp($testImage->image);

    assertDatabaseHas('test_images', [
        'image' => $this->webpImage,
    ]);
});

it('must resize as needed', function () {
    $testImage = TestModel::find(1);

    $path = $testImage->resize('image', 400, 400);

    Storage::disk()
        ->assertExists(ImageToWebp::toRelativePath($path));

    Storage::disk()
        ->assertExists(ImageToWebp::getOldImageRelativePath());
});

it('must convert and overwrite all images in the directory ', function () {
    Artisan::call('public:to-webp --overwrite');

    // ensure that other files with other extensions are not deleted!
    Storage::disk()
        ->assertExists($this->webpImage);

    // ensure that old files are deleted!
    Storage::disk()
        ->assertMissing($this->image);
});
it('must convert and keep all images in the directory ', function () {
    Artisan::call('public:to-webp');

    Storage::disk()
        ->assertExists($this->webpImage);

    // ensure that old files are there!
    Storage::disk()
        ->assertExists($this->webpImage);
});

it('command must convert image field in the database ', function () {
    Artisan::call('images:to-webp', [
        'model' => TestModel::class,
        'attribute' => 'image',
    ]);


    assertDatabaseHas('test_images', [
        'image' => $this->webpImage,
    ]);

    assertDatabaseMissing('test_images', [
        'image' => $this->image,
    ]);
});
test('command must convert all the image fields in the database ', function () {
    Artisan::call('images:to-webp
    EyadHamza\\\LaravelWebp\\\Tests\\\TestSupport\\\Models\\\TestModel');

    assertDatabaseHas('test_images', [
        'image' => $this->webpImage,
    ]);

    assertDatabaseMissing('test_images', [
        'image' => $this->image,
    ]);
});
it('can support multiple image fields url in the database', function () {
    $testImage = TestModel::find(1);

    $testImage->saveImageAsWebp();

    assertDatabaseHas('test_images', [
        'image' => $this->webpImage,
    ]);
});

it('should throw an exception if no image was given', function () {
    $testImage = TestModel::create([
        'image' => null,
    ]);
    $this->expectException(NoImageGivenException::class);

    ImageToWebp::make($testImage->image);

    assertDatabaseHas('test_images', [
        'image' => null,
    ]);
});
it('should throw an exception if the path is for an not image was given', function () {
    $testImage = TestModel::create([
        'image' => 'hellothere',
    ]);
    $this->expectException(NotImageException::class);

    ImageToWebp::make($testImage->image);

    assertDatabaseHas('test_images', [
        'image' => null,
    ]);
});
