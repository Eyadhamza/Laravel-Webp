<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pi\LaravelWebp\Tests\TestCase;

uses(TestCase::class,
    RefreshDatabase::class)
    ->in('Feature');
