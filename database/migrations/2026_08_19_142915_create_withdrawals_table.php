<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->bigIncrements('WithdrawID');

            $table->unsignedBigInteger('UserID');

            $table->decimal('amount', 12, 2);

            $table->enum('type', ['W', 'O'])
                ->after('amount');
                
            $table->enum('withdrawal_method', [
                'Cash',
                'GCash',
                'Maya',
                'Bank Transfer',
            ]);
            
            $table->string('account_name', 255)
                ->nullable();

            $table->string('account_number', 100)
                ->nullable();

            $table->string('payment_reference', 100)
                ->nullable();


            $table->unsignedBigInteger('ProcessedBy')
                ->nullable();

            $table->enum('status', [
                'Pending',
                'Approved',
                'Rejected',
            ])->default('Pending');

            $table->timestamps();

            // Foreign Keys
            $table->foreign('UserID', 'withdrawals_userid_foreign')
                ->references('UserID')
                ->on('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('ProcessedBy', 'withdrawals_processedby_foreign')
                ->references('UserID')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};