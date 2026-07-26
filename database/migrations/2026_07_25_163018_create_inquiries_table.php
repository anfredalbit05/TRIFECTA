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
        Schema::create('inquiries', function (Blueprint $table) {

            $table->id('ID');

            $table->string('name', 150);

            $table->string('email');

            $table->string('subject', 255);

            $table->longText('details');

            $table->string('category', 100)->nullable();

            $table->timestamp('date_read')->nullable();

            $table->timestamps();

            $table->foreignId('AssignedUserID')
                ->nullable()
                ->constrained('users', 'UserID')
                ->cascadeOnUpdate()
                ->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
