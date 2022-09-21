<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bureaus', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('單位');
            $table->string('address', '60')->comment('地址');
            $table->string('TEL', 11)->comment('電話');
            $table->foreignId('user_id')->nullable()->comment('帶頭的');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bureaus');
    }
};
