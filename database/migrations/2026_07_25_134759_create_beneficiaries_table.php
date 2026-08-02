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
        Schema::create('beneficiaries', function (Blueprint $table) {

            $table->id('BeneficiaryID');

            $table->foreignId('UserID')
                ->constrained('users', 'UserID')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Beneficiary Information
            $table->string('fullname', 150);

            $table->date('birthdate');

            $table->string('relationship', 50)->nullable();

            // $table->enum('status', [
            //     'Active',
            //     'Inactive',
            // ])->default('Active');

            $table->boolean('status')->default(true);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
