<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTypesTable extends Migration
{
    public function up()
    {
        Schema::create('product_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->longText('short_description')->nullable();
            $table->longText('description_longue')->nullable();
            $table->string('accounting')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
