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
        Schema::table('scale_answers', function (Blueprint $table) {
            $table->after('answer', function ($table) {
                $table->foreignId('social_worker_id')->default(0)->comment('社工ID');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scale_answers', function (Blueprint $table) {
            $table->dropColumn('social_worker_id');
        });
    }
};
