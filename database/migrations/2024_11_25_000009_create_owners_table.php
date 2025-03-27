<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOwnersTable extends Migration
{
    public function up()
    {
        Schema::create('owners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('address');
            $table->string('zip_code');
            $table->string('city');
            $table->string('email');
            $table->string('phone');
            $table->string('web_site_address');
            $table->string('siret');
            $table->string('capital');
            $table->string('bic');
            $table->string('iban');
            $table->string('hotline_name');
            $table->string('hotline_phone');
            $table->string('hotline_email');
            $table->string('accounting_manager');
            $table->string('accounting_phone');
            $table->string('accounting_email');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
