<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('no_bill', 100)->nullable();
            $table->double('amount', 20, 2)->nullable();
            $table->double('amountInclVAT', 20, 2)->nullable();
            $table->boolean('one_bill_per_period')->default(0);
            $table->date('deadline')->nullable();
            $table->date('billed_at')->nullable();
            $table->date('generated_at')->nullable();
            $table->date('validated_at')->nullable();
            $table->date('sent_at')->nullable();
            $table->boolean('to_be_taken')->default(0);
            $table->date('took_at')->nullable();
            $table->date('recorded_at')->nullable();
            $table->string('file_position', 250)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bills');
    }
};
