<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('unique_key')->unique();
            $table->timestamps();

            $table->index(['user_id', 'slug']);
        });

        $now = now();

        $defaults = [
            'reaction',
            'meme',
            'cute',
            'gaming',
            'wow',
            'sad',
            'happy',
            'other',
        ];

        DB::table('tags')->insert(array_map(fn (string $name): array => [
            'name' => $name,
            'slug' => $name,
            'user_id' => null,
            'unique_key' => 'global:'.$name,
            'created_at' => $now,
            'updated_at' => $now,
        ], $defaults));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
