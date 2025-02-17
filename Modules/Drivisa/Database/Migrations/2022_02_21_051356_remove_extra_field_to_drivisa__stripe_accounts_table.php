<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveExtraFieldToDrivisaStripeAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('email');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('first_name');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('last_name');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('id_number');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('phone');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('city');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('line1');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('postal_code');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('state');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('country');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('birth_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
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
        });
    }
}
