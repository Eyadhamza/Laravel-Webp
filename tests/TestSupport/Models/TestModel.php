<?php

namespace EyadHamza\LaravelWebp\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EyadHamza\LaravelWebp\Traits\HandleWebpConversion;

class TestModel extends Model
{
    use HandleWebpConversion;
    use HasFactory;
    protected $table = 'test_images';

    protected $fillable = [
        'image',
    ];

    protected string $imageField = 'image';
}
