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
            $table->string('name', 100)->unique()->nullable();
            $table->string('address', 100)->nullable();
            $table->string('address_compl', 100)->nullable();
            $table->string('telephone', 10)->nullable();
            $table->string('zip_code', 5);
            $table->string('town_name', 50);
            $table->string('email', 200)->nullable();
            $table->string('old_email_compta', 200)->nullable();
            $table->boolean('prospect');
            $table->string('url', 200)->nullable();
            $table->boolean('fiche');
            $table->date('date_crea');
            $table->integer('authorize_mailing')->default(1);
            $table->string('accounting', 20)->nullable();
            $table->string('reference_ciel', 40);
            $table->boolean('bill_by_email')->default(1);
            $table->boolean('one_bill_per_period')->default(1);
            $table->boolean('to_be_taken')->default(1);
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
