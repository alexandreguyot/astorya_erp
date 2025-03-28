<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToContractsTable extends Migration
{
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id', 'company_fk_10285826')->references('id')->on('companies');
            $table->unsignedBigInteger('type_period_id')->nullable();
            $table->foreign('type_period_id', 'type_period_fk_10285826')->references('id')->on('period_types');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->foreign('contract_id', 'contract_fk_10285826')->references('id')->on('contracts');
            $table->unsignedBigInteger('type_product_id')->nullable();
            $table->foreign('type_product_id', 'type_product_fk_10285826')->references('id')->on('types_products');
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id', 'company_fk_10285827')->references('id')->on('companies');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id', 'company_fk_10285828')->references('id')->on('companies');
        });

        Schema::table('contract_product_details', function (Blueprint $table) {
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->foreign('contract_id', 'contract_fk_10285878')->references('id')->on('contracts');
            $table->unsignedBigInteger('type_product_id')->nullable();
            $table->foreign('type_product_id', 'type_product_fk_10285828')->references('id')->on('types_products');
        });
    }
}
