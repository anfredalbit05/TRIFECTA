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
        Schema::create('news_updates', function (Blueprint $table) {

            $table->id('ID');

            // Post Information
            $table->string('title', 255);

            $table->longText('details');

            // Optional Post Image
            $table->string('image')
                ->nullable();

            // Category (Restricted to specific allowed values)
// Category (Nullable String)
            $table->string('category', 100)
                ->nullable()
                ->default(null);

            // Status
            $table->enum('status', [
                'Published',
                'Draft',
                'Archived',
            ])->default('Published');


            // Owner / Creator
            $table->foreignId('UserID')
                ->nullable()
                ->constrained('users', 'UserID')
                ->cascadeOnUpdate()
                ->nullOnDelete();


            $table->timestamps();

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_updates');
    }
};
