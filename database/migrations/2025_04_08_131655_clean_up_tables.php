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
        Schema::table('contract_product_details', function (Blueprint $table) {
            // Rendre les champs nullable
            $table->timestamp('billing_started_at')->nullable()->change();
            $table->timestamp('billing_terminated_at')->nullable()->change();
            $table->timestamp('last_billed_at')->nullable()->change();
        });

        Schema::table('contracts', function (Blueprint $table) {
            // Rendre les champs nullable
            $table->timestamp('terminated_at')->nullable()->change();
            $table->timestamp('billed_at')->nullable()->change();
            $table->timestamp('validated_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
