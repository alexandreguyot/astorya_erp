<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contract_product_details', function (Blueprint $table) {
            $table->id();
            $table->longText('designation')->nullable();
            $table->integer('quantity');
            $table->string('capacity', 250)->nullable();
            $table->decimal('monthly_unit_price_without_taxe', 10, 2)->default(0);
            $table->dateTime('billing_started_at');
            $table->dateTime('billing_terminated_at');
            $table->dateTime('last_billed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_product_details');
    }
};
