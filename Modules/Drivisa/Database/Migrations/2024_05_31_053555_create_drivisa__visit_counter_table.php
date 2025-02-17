<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivisaVisitCounterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('drivisa__visit_counter', function (Blueprint $table) {
            $table->id();
            $table->string('visit_type')->unique();
            $table->unsignedBigInteger('counter')->default(0);
            $table->timestamps();
        });

        DB::table('drivisa__visit_counter')->insert([
            ['visit_type' => 'page', 'counter' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['visit_type' => 'ios-app', 'counter' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['visit_type' => 'android-app', 'counter' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('drivisa__visit_counter');
    }
}
