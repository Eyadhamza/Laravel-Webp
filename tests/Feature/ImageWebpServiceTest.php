<?php

use EyadHamza\LaravelWebp\Exceptions\NoImageGivenException;
use EyadHamza\LaravelWebp\Exceptions\NotImageException;

use EyadHamza\LaravelWebp\Services\WebpService;
use EyadHamza\LaravelWebp\Tests\TestSupport\Models\TestModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    Storage::fake();

    $this->image = UploadedFile::fake()
        ->image('image.jpg')
        ->store('public');


    $this->webpImage = Str::replace('.jpg', '.webp', $this->image);

    $this->testModel = TestModel::factory()->create([
        'image' => $this->image,
    ]);
});

it('can cast an image to webp and store it on disk', function () {
    Storage::disk()->assertExists($this->testModel->image);

    Str::endsWith($this->testModel->image, '.webp');
});

it('can overwrite the old image as the default behavior', function () {
    Storage::disk()
        ->assertExists($this->testModel->image);

    Storage::disk()
        ->assertMissing($this->image);
});

it('can prevent overwrite the old image as the user config behavior', function () {
    config()->set('webp.overwrite', false);

    $this->secondImage = UploadedFile::fake()
        ->image('image.jpg')
        ->store('public');

    $this->secondWebpImage = Str::replace('.jpg', '.webp', $this->secondImage);

    $this->secondTestModel = TestModel::factory()->create([
        'image' => $this->secondImage,
    ]);

    Storage::disk()
        ->assertExists($this->secondTestModel->image);

    Storage::disk()
        ->assertExists($this->secondImage);
});

it('can save an image by passing the path', function () {
    $testImage = TestModel::find(1);

    WebpService::make($testImage->image)->save();

    Storage::disk()->assertExists($testImage->image);
});


it('must resize as needed', function () {
    $image = Image::make(Storage::get($this->testModel->image));

    expect($image->width())->toBe(200)
        ->and($image->height())->toBe(200);
});

it('must convert and overwrite all images in the directory ', function () {
    Artisan::call('public:to-webp --overwrite');

    Storage::disk()
        ->assertExists($this->webpImage);

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

    WebpService::make($testImage->image);

    assertDatabaseHas('test_images', [
        'image' => null,
    ]);
});
it('should throw an exception if the path is for an not image was given', function () {
    $testImage = TestModel::create([
        'image' => 'hellothere',
    ]);
    $this->expectException(NotImageException::class);

    WebpService::make($testImage->image);

    assertDatabaseHas('test_images', [
        'image' => null,
    ]);
});
