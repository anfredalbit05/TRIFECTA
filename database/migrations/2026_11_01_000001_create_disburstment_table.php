<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Disburstment table (user-specified name, note the "u").
     *
     * Money paid OUT to implementers/investors, drawn from a collected
     * membership fee (FeeID). Every row is mirrored in `ledgers` with
     * Source 'DB' and a NEGATIVE amount:
     *   status 0 (Unposted) -> ledger Pending
     *   status 1 (Posted)   -> ledger Completed
     *   status 2 (Void)     -> ledger Cancelled
     *
     * The rows are immutable history — never deleted — so the FK uses
     * CASCADE exactly as the user's final DDL specifies (a fee deletion
     * would take its disbursements with it; fees are not deletable in
     * the UI, so this only guards against direct SQL removals).
     */
    public function up(): void
    {
        // The live MySQL database already contains this table (built from
        // the user's own DDL, identical shape) — create only when missing so
        // existing environments are not touched, while fresh installs and
        // the SQLite test suite still get the full table + FK.
        if (Schema::hasTable('Disburstment')) {
            return;
        }

        Schema::create('Disburstment', function (Blueprint $table) {
            $table->id('DID');

            // membership_fees.FeeID is a VARCHAR business key ('FEE001'…),
            // so the FK must be a string too.
            $table->string('FeeID')->nullable();

            $table->decimal('amount', 12, 2);

            $table->longText('remarks')->nullable();

            // 0 = Unposted, 1 = Posted, 2 = Void.
            $table->integer('status')->default(0);

            $table->timestamps();

            $table->index('status');
            $table->index('FeeID');

            $table->foreign('FeeID', 'disbursement_FEEID_foreign')
                ->references('FeeID')->on('membership_fees')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Disburstment');
    }
};
