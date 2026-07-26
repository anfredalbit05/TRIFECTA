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
        Schema::create('activations', function (Blueprint $table) {

            $table->id('ActivationID');

            $table->foreignId('UserID')
                ->constrained('users', 'UserID')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('amount', 12, 2);

            $table->enum('type', [
                'W',
                'O',
            ]);

            $table->string('referencecode', 100)->unique();

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
        Schema::dropIfExists('activations');
    }
};
