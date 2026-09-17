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


            /*
            |--------------------------------------------------------------------------
            | Member Being Activated
            |--------------------------------------------------------------------------
            */

            $table->foreignId('UserID')
                ->constrained('users', 'UserID')
                ->cascadeOnUpdate()
                ->restrictOnDelete();


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
                'Expired',
            ])->default('Pending');


            $table->timestamps();

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

