<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('setup_at')->nullable();
            $table->datetime('established_at')->nullable();
            $table->datetime('started_at')->nullable();
            $table->datetime('terminated_at')->nullable();
            $table->datetime('billed_at')->nullable();
            $table->datetime('validated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
