<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('address', 50);
            $table->string('zip_code', 5);
            $table->string('city', 50);
            $table->string('email', 50);
            $table->string('phone', 15);
            $table->string('web_site_address', 50);
            $table->string('siret', 20);
            $table->string('capital', 10);
            $table->string('bic', 30);
            $table->string('iban', 30);
            $table->string('hotline_name', 50);
            $table->string('hotline_phone', 15);
            $table->string('hotline_email', 50);
            $table->string('accounting_manager', 50);
            $table->string('accounting_phone', 15);
            $table->string('accounting_email', 50);
            $table->timestamps(0);
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('owners');
    }
};
