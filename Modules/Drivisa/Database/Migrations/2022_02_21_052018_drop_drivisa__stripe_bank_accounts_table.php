<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Drivisa\Entities\StripeBankAccount;

class DropDrivisaStripeBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('drivisa__stripe_bank_accounts');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('drivisa__stripe_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stripe_account_id')->constrained('drivisa__stripe_accounts')
                ->onDelete('cascade');
            $table->string('country');
            $table->string('currency');
            $table->string('routing_number');
            $table->string('account_number');
            $table->string('account_holder_name');
            $table->string('account_holder_type')->default(StripeBankAccount::account_type['individual']);
            $table->softDeletes();
            $table->timestamps();
        });
    }
}
