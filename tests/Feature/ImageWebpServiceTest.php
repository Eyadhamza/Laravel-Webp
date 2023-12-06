<?php

use EyadHamza\LaravelWebp\Exceptions\NoImageGivenException;
use EyadHamza\LaravelWebp\Exceptions\NotImageException;

use EyadHamza\LaravelWebp\Services\WebpService;
use EyadHamza\LaravelWebp\Support\PathConversionSupport;
use EyadHamza\LaravelWebp\Tests\TestSupport\Models\TestModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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
    $image = UploadedFile::fake()
        ->image('image.jpg')
        ->store('public');

    $webpImage = Str::replace('.jpg', '.webp', $image);

    Artisan::call('public:to-webp --overwrite');

    Storage::disk()
        ->assertExists($webpImage);

    // ensure that old files are there!
    Storage::disk()
        ->assertMissing($image);
});
it('must convert and keep all images in the directory ', function () {
    $image = UploadedFile::fake()
        ->image('image.jpg')
        ->store('public');

    $webpImage = Str::replace('.jpg', '.webp', $image);

    Artisan::call('public:to-webp');

    Storage::disk()->assertExists($webpImage);

    // ensure that old files are there!
    Storage::disk()
        ->assertExists($image);
});

it('command must convert image field in the database ', function () {
    $image = UploadedFile::fake()
        ->image('image.jpg')
        ->store('public');

    $webpImage = Str::replace('.jpg', '.webp', $image);

    DB::table('test_images')->insert([
        'image' => $image,
    ]);

    Artisan::call('images:to-webp', [
        'model' => TestModel::class,
    ]);

    assertDatabaseHas('test_images', [
        'image' => PathConversionSupport::appendWidthAndHeightToImageName($webpImage, 200, 200),
    ]);
});


it('should throw an exception if no image was given', function () {
    $this->expectException(NoImageGivenException::class);

    TestModel::create([
        'image' => null,
    ]);

});
it('should throw an exception if the path is for an not image was given', function () {
    $this->expectException(NotImageException::class);

    $testImage = TestModel::create([
        'image' => 'hellothere',
    ]);

});

it('should not make the changes twice', function () {
    $testImage = TestModel::find(1);

    $oldPath = $testImage->image;

    $testImage->update([
        'image' => $testImage->image,
    ]);

    Storage::disk()->assertExists($oldPath);
});
