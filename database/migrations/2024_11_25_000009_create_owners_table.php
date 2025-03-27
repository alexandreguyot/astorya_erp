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
            $table->string('zip_code', 10);
            $table->string('city', 50);
            $table->string('tel_web_part', 40);
            $table->string('account_part', 30);
            $table->string('email', 50);
            $table->string('tel', 15);
            $table->string('website', 50);
            $table->string('number_siret', 20);
            $table->string('capital', 10);
            $table->string('bic', 30);
            $table->string('iban', 30);
            $table->string('service_web', 30);
            $table->string('service_hotline', 50);
            $table->string('tel_hotline', 15);
            $table->string('email_hotline', 50);
            $table->string('service_account', 50);
            $table->string('tel_account', 15);
            $table->string('email_account', 50);
            $table->timestamps(0); // Timestamps for created_at and updated_at
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('owners');
    }
};
