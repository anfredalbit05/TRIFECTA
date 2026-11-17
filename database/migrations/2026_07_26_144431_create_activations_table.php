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

            $table->unsignedBigInteger('VID')->nullable();


            /*
            |--------------------------------------------------------------------------
            | Member Being Activated
            |--------------------------------------------------------------------------
            */

            $table->foreignId('UserID')
                ->constrained('users', 'UserID')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('VID', 'activations_variantid_foreign')
                ->references('VID')->on('variants')
                ->cascadeOnUpdate()
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Activation Amount
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'amount',
                12,
                2
            );


            /*
            |--------------------------------------------------------------------------
            | Activation Type
            |
            | W = Walk-in
            | O = Online
            |--------------------------------------------------------------------------
            */

            $table->enum('type', [
                'W',
                'O',
            ]);


            /*
            |--------------------------------------------------------------------------
            | Payment Method
            |--------------------------------------------------------------------------
            */

            $table->enum('payment_method', [
                'Cash',
                'GCash',
                'Maya',
                'Bank Transfer',
            ]);


            /*
            |--------------------------------------------------------------------------
            | Referral Code
            |
            | Entered by the member.
            | This is the uplineCode of the referral user.
            |--------------------------------------------------------------------------
            */

            $table->string(
                'upline_code',
                100
            );


            /*
            |--------------------------------------------------------------------------
            | Payment Reference
            |
            | Required only for online payments.
            | Null for Cash payments.
            |--------------------------------------------------------------------------
            */

            $table->string(
                'payment_reference',
                100
            )->nullable();


            /*
            |--------------------------------------------------------------------------
            | Referral User
            |
            | The UserID of the user whose
            | uplineCode matches referral_code.
            |--------------------------------------------------------------------------
            */

            $table->foreignId('ProcessedBy')
                ->nullable()
                ->constrained(
                    'users',
                    'UserID'
                )
                ->cascadeOnUpdate()
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Activation Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'Pending',
                'Approved',
                'Rejected',
            ])->default('Pending');

            /*
            |--------------------------------------------------------------------------
            | Satellite Provisioning
            |--------------------------------------------------------------------------
            | isFree marks the free members created when a satellite approval
            | provisions them; satelliteID records which satellite the
            | activation belongs to. The satellite foreign key is attached by
            | 2026_10_04_000001 once the `satellites` table exists.
            */

            $table->boolean('isFree')->default(false);
            $table->unsignedBigInteger('satelliteID')->nullable();


            $table->timestamps();

            $table->string('cocogen_policy_number', 50)->nullable();
            $table->string('receipt_number', 50)->nullable();
            $table->string('policy_number', 50)->nullable();
            $table->date('policy_effectivity_date')->nullable();
            $table->date('policy_expiration_date')->nullable();
            $table->string('cashier_name')->nullable();
            $table->string('cashier_signature')->nullable();

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'activations'
        );
    }
};

