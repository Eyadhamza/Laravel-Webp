<?php

namespace Pi\LaravelWebp\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pi\LaravelWebp\Traits\HandleWebpConversion;

class TestModel extends Model
{
    protected $table = 'test_images';

    use HandleWebpConversion, HasFactory;

    protected $fillable = [
        'image'
    ];

    protected string $imageField = 'image';
}
