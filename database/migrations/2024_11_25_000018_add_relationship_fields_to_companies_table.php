<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCompaniesTable extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->nullable();
            $table->foreign('city_id', 'city_fk_10285233')->references('id')->on('cities');
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->foreign('bank_account_id', 'bank_account_fk_10285233')->references('id')->on('bank_accounts');
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->foreign('contact_id', 'contact_fk_10285234')->references('id')->on('contacts');
        });
    }
}
