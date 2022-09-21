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
        Schema::table('news', function (Blueprint $table) {
            $table->string('title')->comment('消息標題')->change();
            $table->text('content')->comment('消息內容')->change();
            $table->foreignId('news_types_id')->nullable()->comment('消息類型ID')->after('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('title')->comment('最新消息標題')->change();
            $table->text('content')->comment('最新消息內容')->change();
            $table->dropColumn('news_types_id');
        });
    }
};
