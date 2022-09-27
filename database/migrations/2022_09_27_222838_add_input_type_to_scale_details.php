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
        Schema::table('scale_details', function (Blueprint $table) {
            $table->after('option', function ($table) {
                $table->enum('input_type', ['text_string', 'date', 'radiobox', 'checkbox']);
                $table->string('tips')->nullable();
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
        Schema::table('scale_details', function (Blueprint $table) {
            $table->dropColumn('input_type');
            $table->dropColumn('tips');
        });
    }
};
