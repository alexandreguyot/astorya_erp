<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('designation_long', 1000)->nullable();
            $table->integer('quantity')->nullable();
            $table->string('capacity', 250)->nullable();
            $table->float('price_unit_monthly_excl_tax', 10, 2)->nullable();
            $table->date('rolled_out_at')->nullable();
            $table->date('terminated_at')->nullable();
            $table->date('billed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
