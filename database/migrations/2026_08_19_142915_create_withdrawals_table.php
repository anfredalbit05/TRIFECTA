<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('withdrawals')) {
            return;
        }

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->bigIncrements('WithdrawID');

            $table->unsignedBigInteger('UserID');

            $table->decimal('requested_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('amount', 12, 2);

            $table->enum('type', ['W', 'O']);

            $table->enum('withdrawal_method', [
                'Cash',
                'GCash',
                'Maya',
                'Bank Transfer',
            ]);

            $table->string('bank_name', 255)->nullable();

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

            $table->unsignedBigInteger('confirmed_by')->nullable();

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

        // wallets.withdrawID / ledgers.withdrawID point here. Both columns are
        // declared in their own create migrations; the constraints are
        // attached from this one because `withdrawals` only exists from here on.
        foreach (['wallets', 'ledgers'] as $tableName) {
            $hasConstraint = collect(Schema::getForeignKeys($tableName))->contains(
                fn (array $foreignKey) => in_array('withdrawID', $foreignKey['columns'], true)
            );

            if ($hasConstraint || DB::getDriverName() !== 'mysql') {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->foreign('withdrawID', $tableName.'_withdrawid_foreign')
                    ->references('WithdrawID')->on('withdrawals')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
