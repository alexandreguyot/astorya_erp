<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodTypesTable extends Migration
{
    public function up()
    {
        Schema::create('period_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->integer('nb_month');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
