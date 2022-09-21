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
        Schema::table('users', function (Blueprint $table) {
            $table->after('email', function ($table) {
                $table->string('ID_num', 10)->unique()->comment('身分證字號');
                $table->enum('gender', ['男', '女', '其他'])->comment('性別');
                $table->date('birth')->comment('生日');
                $table->string('address_1', 60)->comment('聯絡住址');
                $table->string('address_2', 60)->nullable()->comment('戶籍住址');
                $table->string('phone', 14)->comment('手機號碼');
                $table->string('TEL', 11)->nullable()->comment('電話號碼');
                $table->foreignId('bureau_id')->comment('單位');
            });
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ID_num', 'gender', 'birth', 'address_1', 'address_2', 'phone', 'TEL', 'bureau_id']);
            $table->dropSoftDeletes();
        });
    }
};
