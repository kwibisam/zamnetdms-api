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
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->integer('version_number');
            $table->string('file_path')->nullable();
            $table->string('content')->nullable();
            $table->foreignId('document_id')
            ->constrained(table: 'documents')
            ->cascadeOnDelete();
            $table->foreignId('created_by')
            ->nullable()
            ->constrained(table: 'users')
            ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};
