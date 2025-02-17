<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropReplyFromDrivisaComplaintTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasColumn('drivisa__complaint', 'reply')) {
            Schema::table('drivisa__complaint', function (Blueprint $table) {
                $table->dropColumn('reply');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__complaint', function (Blueprint $table) {
            $table->unsignedInteger('reply')->nullable();
        });
    }
}
