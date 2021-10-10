<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use EyadHamza\LaravelWebp\Tests\TestCase;

uses(
    TestCase::class,
    RefreshDatabase::class
)
    ->in('Feature');
