<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Links a draw cycle to the news feed announcement that lists its winners,
     * so the same post is updated on every spin instead of posting one per win.
     */
    public function up(): void
    {
        Schema::table('spin_draws', function (Blueprint $table) {
            $table->unsignedBigInteger('AnnouncementID')
                ->nullable()
                ->after('completed_at');

            $table->index('AnnouncementID');
        });
    }

    public function down(): void
    {
        Schema::table('spin_draws', function (Blueprint $table) {
            $table->dropIndex(['AnnouncementID']);
            $table->dropColumn('AnnouncementID');
        });
    }
};
