<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_rum')->nullable();
            $table->datetime('effective_start_date')->nullable();
            $table->string('bic')->nullable();
            $table->string('iban')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
