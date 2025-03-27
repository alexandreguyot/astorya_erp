<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('address_compl')->nullable();
            $table->string('email')->nullable();
            $table->string('accounting')->nullable();
            $table->string('ciel_reference')->nullable();
            $table->boolean('send_bill_type')->default(0)->nullable();
            $table->boolean('one_bill_per_period')->default(0)->nullable();
            $table->string('bill_payment_methood')->nullable();
            $table->longText('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
