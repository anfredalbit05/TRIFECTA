<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the acknowledgement-receipt control number to users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('control_number', 50)->nullable()->after('policy_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('control_number');
        });
    }
};
