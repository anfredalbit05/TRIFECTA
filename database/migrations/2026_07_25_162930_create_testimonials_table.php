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
        Schema::create('testimonials', function (Blueprint $table) {

            $table->id('TestimonialsID');

            $table->string('fullname', 150);

            $table->string('designation', 150)->nullable();

            $table->longText('testimony');

            $table->tinyInteger('rate')
                ->unsigned()
                ->default(5);

            $table->enum('status', [
                'Pending',
                'Published',
                'Rejected',
            ])->default('Pending');

            $table->timestamps();

            $table->foreignId('UserID')
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
        Schema::dropIfExists('testimonials');
    }
};
