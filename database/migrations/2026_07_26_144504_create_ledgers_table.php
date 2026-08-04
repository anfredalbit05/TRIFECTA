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
        Schema::create('ledgers', function (Blueprint $table) {

            $table->id('TransactionID');

<<<<<<< HEAD
<<<<<<< HEAD
            $table->unsignedBigInteger('RefID'); //Membership Fee
=======
        $this->member = User::find($this->selectedUserID);
>>>>>>> ef58e6bb3d6f16a88db1d9ed9a9e2e118b00420c
=======
            $table->unsignedBigInteger('RefID'); //Membership Fee
>>>>>>> 859c1d2343e4c95b0d797d545229f05478ae7103

            $table->decimal('amount', 12, 2);

            $table->enum('Source', [
                'MF', // Membership Fee
                'DR', // Direct Referral
                'OV', // Overrides
                'CL', // Claim
                'RF', // Refund
                'OT', // Others
            ])->default('OT');

            $table->foreignId('ActivationID')
                ->constrained('activations', 'ActivationID')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
=======
        /*
        |--------------------------------------------------------------------------
        | Load Referral User
        |--------------------------------------------------------------------------
        */
        $this->referralUser = null;
>>>>>>> ef58e6bb3d6f16a88db1d9ed9a9e2e118b00420c
=======
>>>>>>> 859c1d2343e4c95b0d797d545229f05478ae7103



            $table->foreignId('ActivationID')
                ->constrained('activations', 'ActivationID')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->enum('status', [
                'Pending',
                'Completed',
                'Cancelled',
            ])->default('Completed');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};