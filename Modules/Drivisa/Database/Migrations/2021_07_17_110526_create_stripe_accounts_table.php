<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('account_id')->nullable();
            $table->foreignId('instructor_id')->constrained('drivisa__instructors')
                ->onDelete('cascade');
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('id_number');
            $table->string('phone');
            $table->string('city');
            $table->string('line1');
            $table->string('postal_code');
            $table->string('state');
            $table->string('country');
            $table->date('birth_date');
            $table->softDeletes();
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
        Schema::dropIfExists('drivisa__stripe_accounts');
    }
}
