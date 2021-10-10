<?php

namespace Pi\LaravelWebp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pi\LaravelWebp\Traits\HandleWebpConversion;

class TestImage extends Model
{
    use HandleWebpConversion, HasFactory;
    protected $fillable = [
        'image'
    ];

    protected string $imageField = 'image';
}
