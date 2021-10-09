<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestImagesTable extends Migration
{
    public function up()
    {
        Schema::create('test_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('image');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_images');
    }
}
