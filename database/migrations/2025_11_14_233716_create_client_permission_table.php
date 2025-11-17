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
        Schema::connection('da')->create('client_permission', function (Blueprint $table) {
            $table->unsignedInteger('client_id');
            $table->unsignedBigInteger('permission_id');
            
            // Now add the foreign key constraints
            $table->foreign('client_id')
                ->references('id')
                ->on('Clients')
                ->cascadeOnDelete();
                
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->cascadeOnDelete();
                
            $table->primary(['client_id', 'permission_id']);
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('da')->dropIfExists('client_permission');
    }
};
