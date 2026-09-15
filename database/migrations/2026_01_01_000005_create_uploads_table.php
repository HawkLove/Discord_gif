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
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tag');
            $table->string('visibility');
            $table->string('source_type');
            $table->text('source_url')->nullable();
            $table->text('media_url');
            $table->string('path')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('original_filename')->nullable();
            $table->timestamps();

            $table->index(['visibility', 'user_id']);
            $table->index('tag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
