<?php

use EyadHamza\LaravelWebp\ImageToWebp;

use EyadHamza\LaravelWebp\Tests\TestSupport\Models\TestModel;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\withoutExceptionHandling;

beforeEach(function () {
    TestModel::factory()->create([
        'image' => $this->getTestImageRelativePath(),
        'avatar' => $this->getSecondTestImageRelativePath()
        ]);

    $this->refreshAndClean();
});

it('prepare test', function () {
    expect(true)->toBeTrue();
});

it('can save an image', function () {
    $testImage = TestModel::find(1);

    $testImage->saveImageAsWebp();

    Storage::disk()
        ->assertExists($testImage->image);

    Storage::disk()
        ->assertExists(ImageToWebp::getOldImageRelativePath());
});

it('can overwrite an image', function () {
    withoutExceptionHandling();
    $testImage = TestModel::find(1);

    $testImage->overwriteImageAsWebp();

    Storage::disk()
        ->assertExists($testImage->image);

    Storage::disk()
        ->assertMissing(ImageToWebp::getOldImageRelativePath());
});

it('must modify image url in the database', function () {
    $testImage = TestModel::find(1);

    $testImage->saveImageAsWebp();

    assertDatabaseHas('test_images', [
       'image' => ImageToWebp::getWebpRelativePath($this->getTestImageRelativePath()),
   ]);
});

it('can  save an image by passing the path', function () {
    $testImage = TestModel::find(1);

    ImageToWebp::setPath($testImage->image);
    ImageToWebp::save();

    Storage::disk()
        ->assertExists(ImageToWebp::getOldImageRelativePath());
});

it('can overwrite an image by passing the path', function () {
    $testImage = TestModel::find(1);

    ImageToWebp::setPath($testImage->image);
    ImageToWebp::overwrite();

    Storage::disk()
        ->assertExists(ImageToWebp::getWebpRelativePath($testImage->image));

    Storage::disk()
        ->assertMissing(ImageToWebp::getOldImageRelativePath());
});

it('must modify image url in the database by passing the path', function () {
    $testImage = TestModel::find(1);

    $testImage->saveImageAsWebp($testImage->image);

    assertDatabaseHas('test_images', [
        'image' => ImageToWebp::getWebpRelativePath($this->getTestImageRelativePath()),
    ]);
});

it('must resize as needed', function () {
    $testImage = TestModel::find(1);

    $path = $testImage->resize('image',400, 400);

    Storage::disk()
        ->assertExists(ImageToWebp::toRelativePath($path));

    Storage::disk()
        ->assertExists($testImage->image);

});

it('must convert and overwrite all images in the directory ', function () {
    Artisan::call('public:to-webp --overwrite');

    Storage::disk()
        ->assertExists(ImageToWebp::getWebpRelativePath($this->getTestImageRelativePath()));

    // ensure that other files with other extensions are not deleted!
    Storage::disk()
        ->assertExists($this->getTempImageRelativePath());

    // ensure that old files are deleted!
    Storage::disk()
        ->assertMissing($this->getTestImageRelativePath());
});
it('must convert and keep all images in the directory ', function () {
    Artisan::call('public:to-webp');

    Storage::disk()
        ->assertExists(ImageToWebp::getWebpRelativePath($this->getTestImageRelativePath()));

    // ensure that other files with other extensions are not deleted!
    Storage::disk()
        ->assertExists($this->getTempImageRelativePath());

    // ensure that old files are there!
    Storage::disk()
        ->assertExists($this->getTestImageRelativePath());
});

it('must convert image field in the database ', function () {
    Artisan::call('images:to-webp
    EyadHamza\\\LaravelWebp\\\Tests\\\TestSupport\\\Models\\\TestModel
    image');

    assertDatabaseHas('test_images', [
        'image' => ImageToWebp::getWebpFullPath($this->getTestImageRelativePath()),
    ]);

    assertDatabaseMissing('test_images', [
        'image' => ImageToWebp::toFullPath($this->getTestImageRelativePath()),
    ]);
});
it('can support multiple image fields url in the database', function () {
    $testImage = TestModel::find(1);

    $testImage->saveImageAsWebp();

    assertDatabaseHas('test_images', [
        'image' => ImageToWebp::getWebpRelativePath($this->getTestImageRelativePath()),
        'avatar' => ImageToWebp::getWebpRelativePath($this->getSecondTestImageRelativePath())
    ]);
});


afterEach(fn () => $this->refreshAndClean());
