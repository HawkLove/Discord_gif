<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('uploads', function (Blueprint $table) {
            $table->foreignId('tag_id')->nullable()->after('user_id')->constrained()->restrictOnDelete();
        });

        DB::table('uploads')->select('id', 'user_id', 'tag')->orderBy('id')->each(function (object $upload): void {
            $name = trim((string) $upload->tag);
            $slug = Str::slug($name);
            $slug = $slug !== '' ? $slug : Str::lower($name);

            $tagId = DB::table('tags')
                ->whereNull('user_id')
                ->where('slug', $slug)
                ->value('id');

            if ($tagId === null) {
                $tagId = DB::table('tags')
                    ->where('user_id', $upload->user_id)
                    ->where('slug', $slug)
                    ->value('id');
            }

            if ($tagId === null) {
                $tagId = DB::table('tags')->insertGetId([
                    'name' => $name !== '' ? $name : 'other',
                    'slug' => $slug !== '' ? $slug : 'other',
                    'user_id' => $upload->user_id,
                    'unique_key' => 'user:'.$upload->user_id.':'.($slug !== '' ? $slug : 'other'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('uploads')->where('id', $upload->id)->update(['tag_id' => $tagId]);
        });

        Schema::table('uploads', function (Blueprint $table) {
            $table->dropIndex(['tag']);
            $table->dropColumn('tag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uploads', function (Blueprint $table) {
            $table->string('tag')->nullable()->after('user_id');
        });

        $names = DB::table('tags')->pluck('name', 'id');

        DB::table('uploads')->select('id', 'tag_id')->orderBy('id')->each(function (object $upload) use ($names): void {
            DB::table('uploads')->where('id', $upload->id)->update([
                'tag' => $names[$upload->tag_id] ?? 'other',
            ]);
        });

        Schema::table('uploads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tag_id');
            $table->index('tag');
        });
    }
};
