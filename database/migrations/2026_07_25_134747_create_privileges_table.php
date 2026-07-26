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
        Schema::create('privileges', function (Blueprint $table) {

            $table->id('PrivilegeID');

            $table->foreignId('RoleID')
                ->constrained('roles', 'RoleID')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('ModuleID')
                ->constrained('modules', 'ModuleID')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Permissions
            $table->boolean('can_create')->default(false);
            $table->boolean('can_read')->default(false);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_print')->default(false);
            $table->boolean('can_change_status')->default(false);

            $table->timestamps();

            // Prevent duplicate role-module assignments
            $table->unique(['RoleID', 'ModuleID']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('privileges');
    }
};