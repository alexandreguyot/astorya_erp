<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('type_vats', function (Blueprint $table) {
            $table->id();
            $table->integer('code_vat');
            $table->float('percent', 10, 2);
            $table->string('account_vat', 20);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('type_vats');
    }
};
