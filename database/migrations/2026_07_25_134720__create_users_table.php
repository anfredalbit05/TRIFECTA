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
        Schema::create('users', function (Blueprint $table) {
            $table->id('UserID');

            $table->foreign('BranchID')
            ->references('BranchID')
            ->on('branches')
            ->nullOnDelete();

            // Account Information
            $table->string('username', 50)->unique();
            $table->string('password');

            // Personal Information
            $table->string('lastname', 100);
            $table->string('firstname', 100);
            $table->string('middlename', 100)->nullable();

            $table->string('gender', 20)->nullable();
            $table->string('civilstatus', 20)->nullable();

            $table->date('birthdate')->nullable();

            $table->string('contactno', 20)->nullable();

            $table->text('address')->nullable();
            $table->string('occupation')->nullable();

            // Emergency Contact Information
            $table->string('emergencyName', 255)->nullable();
            $table->string('emergencyRelationship', 255)->nullable();
            $table->string('emergencyContactno', 50)->nullable();
            $table->text('emergencyAddress')->nullable();

            // Location Information
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();

            // Referral Information
            $table->string('referralCode', 50)->unique()->nullable();
            $table->string('uplineCode', 50)->nullable();

            // Foreign Keys
            $table->unsignedBigInteger('RoleID');

            $table->string('position')->nullable()->default('client');

            // Status
            $table->enum('status', [
                'Active',
                'Inactive',
                'Suspended',
            ])->default('Inactive');

            $table->string('signature', 255)->nullable();

            $table->string('policy_number', 50)->nullable();
            $table->date('policy_effectivity_date')->nullable();
            $table->date('policy_expiration_date')->nullable();

            $table->rememberToken();

            $table->timestamps();

            // Profile Picture
            $table->string('profilepic')
                ->nullable()
                ->default('images/profiles/default-avatar.png');

            $table->foreign('RoleID')
                ->references('RoleID')
                ->on('roles')
                ->cascadeOnUpdate()
                ->restrictOnDelete();



        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['BranchID']);
            $table->dropColumn('BranchID');
        });
    }
};
