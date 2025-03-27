<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('types_product', function (Blueprint $table) {
            $table->id();
            $table->string('code_article', 20)->nullable();
            $table->string('designation_short', 50)->nullable();
            $table->string('designation_long', 1000)->nullable();
            $table->string('accounting', 20)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('types_product');
    }
};
