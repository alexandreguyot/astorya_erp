<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('accounting_histos', function (Blueprint $table) {
            $table->id();
            $table->longText('journal');
            $table->dateTime('date', 6);
            $table->longText('no_bill');
            $table->longText('account_number');
            $table->longText('label');
            $table->decimal('debit_amount', 10, 2);
            $table->decimal('credit_amount', 10, 2);
            $table->dateTime('deadline', 6);
            $table->longText('product_code')->nullable();
            $table->longText('product_short_description')->nullable();
            $table->longText('company_name')->nullable();
            $table->longText('company_accounting')->nullable();
            $table->longText('company_ciel_reference')->nullable();
            $table->longText('payment_code')->nullable();
            $table->dateTime('creation_date', 6);
            $table->dateTime('last_modified_date', 6);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounting_histos');
    }
};
