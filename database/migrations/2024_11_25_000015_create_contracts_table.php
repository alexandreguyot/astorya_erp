<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->dateTime('setup_at');
            $table->dateTime('established_at');
            $table->dateTime('started_at');
            $table->dateTime('terminated_at');
            $table->dateTime('billed_at');
            $table->dateTime('validated_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
