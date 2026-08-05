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
        Schema::create('branches', function (Blueprint $table) {

            $table->id('BranchID');

            $table->string('branchname', 150);
            // $table->string('type', 50);
            $table->text('address');

            // Status
            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->foreignId('UserID')
                            ->constrained('users', 'UserID')
                            ->cascadeOnUpdate()
                            ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
