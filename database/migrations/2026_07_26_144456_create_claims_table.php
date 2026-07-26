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
        Schema::create('claims', function (Blueprint $table) {

            $table->id('ClaimID');

            $table->foreignId('UserID')
                ->constrained('users', 'UserID')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('BentID')
                ->constrained('benefits', 'BentID')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('amount', 12, 2);

            $table->foreignId('ProcessedBy')
                ->nullable()
                ->constrained('users', 'UserID')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->enum('status', [
                'Pending',
                'Approved',
                'Rejected',
            ])->default('Pending');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
