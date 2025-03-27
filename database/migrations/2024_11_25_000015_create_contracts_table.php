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
            $table->date('set_up_at')->nullable();
            $table->date('engagement_initial_at')->nullable();
            $table->date('terminated_at')->nullable();
            $table->date('billed_at')->nullable();
            $table->date('validated_at')->nullable();
            $table->string('observations', 250)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
