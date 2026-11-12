<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Which variants a branch is allowed to handle. The disbursement tab
     * reads this to keep a branch from seeing (or drawing from) variants
     * it was never granted; users.branchCode carries branches.BranchID.
     *
     * The live database already contains this table (built from the admin's
     * own DDL), so it is only created when missing — fresh installs and the
     * SQLite test suite then get the same structure.
     */
    public function up(): void
    {
        if (Schema::hasTable('branch_variant_grants')) {
            return;
        }

        // `variants` is a production-only structure the migrations do not
        // define; only constrain the FK where it already exists.
        $hasVariants = Schema::hasTable('variants');

        Schema::create('branch_variant_grants', function (Blueprint $table) use ($hasVariants) {
            $table->id('GrantID');

            $table->unsignedBigInteger('BranchID');
            $table->unsignedBigInteger('VID');

            // enum('Active','Inactive') on MySQL; portable string here.
            $table->string('status', 10)->default('Active');

            $table->timestamps();

            $table->unique(['BranchID', 'VID'], 'unique_branch_variant');
            $table->index('VID', 'fk_bvg_variant_index');

            $table->foreign('BranchID', 'fk_bvg_branch')
                ->references('BranchID')->on('branches')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            if ($hasVariants) {
                $table->foreign('VID', 'fk_bvg_variant')
                    ->references('VID')->on('variants')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_variant_grants');
    }
};
