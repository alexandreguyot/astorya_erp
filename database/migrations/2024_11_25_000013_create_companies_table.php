<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('address_compl', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('accounting', 20)->nullable();
            $table->string('ciel_reference', 40)->nullable();
            $table->integer('send_bill_type');
            $table->boolean('one_bill_per_period');
            $table->integer('bill_payment_method');
            $table->string('observations', 1000)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
