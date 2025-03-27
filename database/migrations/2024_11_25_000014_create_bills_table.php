<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_bill')->nullable();
            $table->float('amount', 10, 2)->nullable();
            $table->float('amount_vat_included', 10, 2)->nullable();
            $table->boolean('one_bill_per_period')->default(0)->nullable();
            $table->datetime('started_at')->nullable();
            $table->date('billed_at')->nullable();
            $table->datetime('generated_at')->nullable();
            $table->datetime('validated_at')->nullable();
            $table->datetime('sent_at')->nullable();
            $table->boolean('to_be_collected')->default(0)->nullable();
            $table->datetime('collected_at')->nullable();
            $table->datetime('recorded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
