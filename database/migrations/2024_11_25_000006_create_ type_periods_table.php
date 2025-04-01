<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypePeriodsTable extends Migration
{
    public function up()
    {
        Schema::create('type_periods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->integer('nb_month');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
