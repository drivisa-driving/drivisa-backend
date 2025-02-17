<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeFieldsToDrivisaStripeAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->string('account_holder_name')->nullable();
            $table->string('account_holder_type')->nullable();
            $table->string('account_number')->nullable();
            $table->string('country')->nullable();
            $table->string('currency')->nullable();
            $table->string('fingerprint')->nullable();
            $table->string('last4')->nullable();
            $table->string('routing_number')->nullable();
            $table->string('status')->nullable();
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
            $table->dropColumn('account_holder_name');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('account_holder_type');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('account_number');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('country');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('fingerprint');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('last4');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('routing_number');
        });

        Schema::table('drivisa__stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
