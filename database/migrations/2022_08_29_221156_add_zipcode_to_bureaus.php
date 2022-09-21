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
        Schema::table('bureaus', function (Blueprint $table) {
            $table->string('zipcode', 5)->comment('郵遞區號')->after('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bureaus', function (Blueprint $table) {
            $table->dropColumn('zipcode');
        });
    }
};
