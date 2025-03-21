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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('isForm')->default(false);
            $table->boolean('isEditable')->default(false);
            $table->boolean('isFile')->default(false);
            $table->foreignId('created_by')
            ->nullable()
            ->constrained(table: 'users')
            ->nullOnDelete();
            
            // $table->unsignedBigInteger('department_id');
            // $table->foreign('department_id')
            // ->references('id')
            // ->on('departments')
            // ->restrictOnDelete();

            $table->unsignedBigInteger('document_type');
            $table->foreign('document_type')
            ->references('id')
            ->on('document_types')
            ->cascadeOnDelete();

            $table->unsignedBigInteger('workspace_id');
            $table->foreign('workspace_id')
            ->references('id')
            ->on('workspaces')
            ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
