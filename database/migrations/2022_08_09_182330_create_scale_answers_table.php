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
        Schema::create('scale_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->comment('長者ID');
            $table->foreignId('scale_order_id')->nullable()->comment('量表主表ID');
            $table->json('answer')->comment('答案');
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
        Schema::dropIfExists('scale_answers');
    }
};
