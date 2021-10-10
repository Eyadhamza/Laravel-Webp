<?php

use Illuminate\Http\UploadedFile;
use Pi\LaravelWebp\Models\TestImage;

beforeEach(function (){
   Storage::fake('images');
   TestImage::factory()->create(
       ['image' => UploadedFile::fake()->image('avatar.png'),]
   );
});

it('can test', function () {
    expect(true)->toBeTrue();
});

it('can save an image', function () {

});
