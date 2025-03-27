<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lastname')->nullable();
            $table->string('firstname')->nullable();
            $table->string('title')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_director')->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
