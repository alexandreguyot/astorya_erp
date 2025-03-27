<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToBillsTable extends Migration
{
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id', 'company_fk_10285799')->references('id')->on('companies');
            $table->unsignedBigInteger('type_period_id')->nullable();
            $table->foreign('type_period_id', 'type_period_fk_10285800')->references('id')->on('period_types');
        });
    }
}
