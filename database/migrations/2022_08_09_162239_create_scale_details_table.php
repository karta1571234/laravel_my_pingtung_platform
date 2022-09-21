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
        Schema::create('scale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scale_order_id')->comment('量表主表ID');
            $table->text('question')->comment('量表問題');
            $table->json('option')->comment('選項');
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
        Schema::dropIfExists('scale_details');
    }
};
