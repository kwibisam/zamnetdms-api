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
        Schema::create('user_workspace', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_default')->default(false);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('workspace_id');
        
            // // Ensure a user can only have one "default" workspace
            // $table->unique(['user_id', 'is_default']);
        
            // Ensure a user cannot have the same workspace multiple times
            $table->unique(['user_id', 'workspace_id']);
        
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('user_workspace');
    }
};
