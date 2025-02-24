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
        //
        Schema::create('document_tag', function(Blueprint $table)
        {
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('tag_id');
            $table->foreign('document_id')
            ->references('id')
            ->on('documents')
            ->cascadeOnDelete();
            $table->foreign('tag_id')
            ->references('id')
            ->on('tags')
            ->cascadeOnDelete();
            $table->timestamps(); // Adds created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_tag');
    }
};
