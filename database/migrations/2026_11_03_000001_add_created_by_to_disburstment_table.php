<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit trail: which user recorded a disbursement.
     *
     * Nullable so the rows that predate the column (and any system-seeded
     * row) stay valid. The column is frozen together with the other
     * financial fields once the disbursement is Posted/Voided.
     */
    public function up(): void
    {
        if (! Schema::hasTable('Disburstments') || Schema::hasColumn('Disburstment', 'CreatedBy')) {
            return;
        }

        Schema::table('Disburstments', function (Blueprint $table) {
            $table->unsignedBigInteger('CreatedBy')->nullable()->after('status');
            $table->index('CreatedBy', 'disbursement_createdby_index');
        });

        // SQLite cannot add a foreign key to an existing table, so the
        // constraint is built only where the engine supports it.
        if (DB::getDriverName() === 'mysql') {
            Schema::table('Disburstments', function (Blueprint $table) {
                $table->foreign('CreatedBy', 'disbursement_createdby_foreign')
                    ->references('UserID')->on('users')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('Disburstments') || ! Schema::hasColumn('Disburstment', 'CreatedBy')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            Schema::table('Disburstments', function (Blueprint $table) {
                $table->dropForeign('disbursement_createdby_foreign');
            });
        }

        Schema::table('Disburstments', function (Blueprint $table) {
            $table->dropIndex('disbursement_createdby_index');
            $table->dropColumn('CreatedBy');
        });
    }
};
