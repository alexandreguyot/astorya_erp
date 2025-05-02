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
            $table->date('billing_started_at')->nullable()->change();
            $table->date('billing_terminated_at')->nullable()->change();
            $table->date('last_billed_at')->nullable()->change();
        });

        Schema::table('contracts', function (Blueprint $table) {
            // Rendre les champs nullable
            $table->date('terminated_at')->nullable()->change();
            $table->date('billed_at')->nullable()->change();
            $table->date('validated_at')->nullable()->change();
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
