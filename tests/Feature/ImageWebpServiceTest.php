<?php

use EyadHamza\LaravelWebp\Exceptions\NoImageGivenException;
use EyadHamza\LaravelWebp\Exceptions\NotImageException;
use EyadHamza\LaravelWebp\ImageToWebp;

use EyadHamza\LaravelWebp\Tests\TestSupport\Models\TestModel;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\withoutExceptionHandling;

beforeEach(function () {
    TestModel::factory()->create([
        'image' => asset('/storage/'.'test.jpg'),
        'avatar' => asset('/storage/'.'test2.jpg'),
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
        ->assertExists($this->getTestImageWebpRelativePath());

    Storage::disk()
        ->assertExists(ImageToWebp::getOldImageRelativePath());
});
it('can save an image with a very small size', function () {
    $testImage = TestModel::find(1);
    $testImage->image = asset('/storage/'.'test3.png');


    $testImage->saveImageAsWebp();

    Storage::disk()
        ->assertExists('public/test3.webp');

    Storage::disk()
        ->assertExists(ImageToWebp::getOldImageRelativePath());
});

it('can save an image with the right name', function () {
    $testImage = TestModel::find(1);

    $testImage->saveImageAsWebp();

    $testImage->saveImageAsWebp();
    Storage::disk()
        ->assertExists($this->getTestImageRelativePath());

    Storage::disk()
        ->assertExists($this->getTestImageWebpRelativePath());

    assertDatabaseHas('test_images', [
        'image' => ImageToWebp::toFullPath($this->getTestImageWebpRelativePath()),
    ]);
});

it('can overwrite an image', function () {
    withoutExceptionHandling();
    $testImage = TestModel::find(1);

    $testImage->overwriteImageAsWebp();

    Storage::disk()
        ->assertExists($this->getTestImageWebpRelativePath());

    Storage::disk()
        ->assertMissing(ImageToWebp::getOldImageRelativePath());
});

it('must modify image url in the database', function () {
    $testImage = TestModel::find(1);

    $testImage->saveImageAsWebp();

    assertDatabaseHas('test_images', [
       'image' => ImageToWebp::toFullPath($this->getTestImageWebpRelativePath()),
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
        'image' => ImageToWebp::toFullPath($this->getTestImageWebpRelativePath()),
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

    Storage::disk()
        ->assertExists($this->getTestImageWebpRelativePath());

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
        ->assertExists($this->getTestImageWebpRelativePath());

    // ensure that other files with other extensions are not deleted!
    Storage::disk()
        ->assertExists($this->getTempImageRelativePath());

    // ensure that old files are there!
    Storage::disk()
        ->assertExists($this->getTestImageRelativePath());
});

it('command must convert image field in the database ', function () {
    Artisan::call('images:to-webp
    EyadHamza\\\LaravelWebp\\\Tests\\\TestSupport\\\Models\\\TestModel
    image');


    assertDatabaseHas('test_images', [
        'image' => ImageToWebp::toFullPath($this->getTestImageWebpRelativePath()),
    ]);

    assertDatabaseMissing('test_images', [
        'image' => ImageToWebp::toFullPath($this->getTestImageRelativePath()),
    ]);
});
test('command must convert all the image fields in the database ', function () {
    Artisan::call('images:to-webp
    EyadHamza\\\LaravelWebp\\\Tests\\\TestSupport\\\Models\\\TestModel');

    assertDatabaseHas('test_images', [
        'image' => ImageToWebp::toFullPath($this->getTestImageWebpRelativePath()),
        'avatar' => ImageToWebp::toFullPath($this->getSecondTestImageWebpRelativePath()),
    ]);

    assertDatabaseMissing('test_images', [
        'image' => ImageToWebp::toFullPath($this->getTestImageRelativePath()),
    ]);
});
it('can support multiple image fields url in the database', function () {
    $testImage = TestModel::find(1);

    $testImage->saveImageAsWebp();

    assertDatabaseHas('test_images', [
        'image' => ImageToWebp::toFullPath($this->getTestImageWebpRelativePath()),
        'avatar' => ImageToWebp::toFullPath($this->getSecondTestImageWebpRelativePath()),
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
afterEach(fn () => $this->refreshAndClean());
