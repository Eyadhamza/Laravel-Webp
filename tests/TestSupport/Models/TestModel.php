<?php

namespace EyadHamza\LaravelWebp\Tests\TestSupport\Models;

use EyadHamza\LaravelWebp\Traits\HandleWebpConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    use HandleWebpConversion;
    use HasFactory;
    protected $table = 'test_images';

    protected $fillable = [
        'image',
        'avatar',
    ];

    protected array $imageFields = [
        'image',
        'avatar',
    ];
}
