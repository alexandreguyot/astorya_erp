<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVatTypesTable extends Migration
{
    public function up()
    {
        Schema::create('vat_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->float('percent', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
