<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::connection('mysql_second')->create('simba_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('trx_id')->nullable();
            $table->string('third_party_trx_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('txn_source', 20)->nullable();
            $table->double('credit_amount', 10, 2)->default(0.00);
            $table->double('debit_amount', 10, 2)->default(0.00);
            $table->string('sender_currency', 3)->default('TZS');
            $table->string('receiver_currency', 3)->default('TZS');
            $table->decimal('charges', 10, 2)->default(0.00);
            $table->string('txn_destination', 20)->nullable();
            $table->string('receiver_fullname')->nullable();
            $table->decimal('partner_charges', 10, 2)->default(0.00);
            $table->string('transaction_type', 100)->nullable();
            $table->string('biller_code', 100)->nullable();
            $table->string('biller_ref', 100)->nullable();
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('exchange_rate', 10, 2)->default(0.00);
            $table->decimal('partner_exchange_rate', 10, 2)->default(0.00);
            $table->string('partner_name', 200)->nullable();
            $table->string('reason', 100)->nullable();
            $table->string('account_no', 60)->default('NO ACCOUNT');
            $table->string('network_type', 50)->nullable();
            $table->enum('status', ['pending', 'deposited', 'sent', 'received', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simba_transactions');
    }
};
