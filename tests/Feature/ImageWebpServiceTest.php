<?php

use function Pest\Laravel\assertDatabaseHas;

use function Pest\Laravel\withoutExceptionHandling;
use Pi\LaravelWebp\ImageToWebp;
use Pi\LaravelWebp\Tests\TestSupport\Models\TestModel;

beforeEach(function () {
    TestModel::factory()->create(
        ['image' => 'public/test.jpg',]
    );
    $this->prepareTestImage();
});

it('can test', function () {
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

    $testImage->saveImageAsWebp($testImage->image);

    Storage::disk()
        ->assertExists($testImage->image);

    Storage::disk()
        ->assertExists(ImageToWebp::getOldImageRelativePath());
});

it('can overwrite an image by passing the path', function () {
    $testImage = TestModel::find(1);

    $testImage->overwriteImageAsWebp($testImage->image);

    Storage::disk()
        ->assertExists($testImage->image);

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

    $path = $testImage->resizeImage(400, 400);

    Storage::disk()
        ->assertExists(ImageToWebp::toRelativePath($path));

    Storage::disk()
        ->assertExists($testImage->image);
});

afterEach(fn () => $this->refreshAndClean());
