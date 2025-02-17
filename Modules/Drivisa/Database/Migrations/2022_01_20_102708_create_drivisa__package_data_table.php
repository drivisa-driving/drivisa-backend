<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivisaPackageDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__package_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('drivisa__packages')->cascadeOnDelete();
            $table->unsignedDecimal('hours');
            $table->unsignedDecimal('hour_charge');
            $table->unsignedBigInteger('amount');
            $table->string('additional_information')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('drivisa__package_data');
    }
}
