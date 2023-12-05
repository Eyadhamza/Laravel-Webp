<?php

namespace EyadHamza\LaravelWebp\Tests\TestSupport\Models;

use EyadHamza\LaravelWebp\Casts\ToWebpCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    use HasFactory;

    protected $table = 'test_images';

    protected $fillable = [
        'image',
        'avatar',
    ];
    protected $casts = [
        'image' => ToWebpCast::class . ':200,200,100',
        'avatar' => ToWebpCast::class,
    ];
}
