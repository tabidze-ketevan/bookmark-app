<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var list<string>
     */
    private array $ownedTables = ['collections', 'tags', 'bookmarks'];

    /**
     * Run the migrations.
     *
     * Each collection, tag and bookmark now belongs to a user. Deleting a user removes their data, so the
     * foreign keys that pointed at those rows are relaxed to cascade (pivot) or null out (bookmark collection).
     */
    public function up(): void
    {
        foreach ($this->ownedTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            });
        }

        $firstUserId = DB::table('users')->orderBy('id')->value('id');

        if ($firstUserId !== null) {
            foreach ($this->ownedTables as $tableName) {
                DB::table($tableName)->whereNull('user_id')->update(['user_id' => $firstUserId]);
            }
        }

        Schema::table('bookmarks', function (Blueprint $table): void {
            $table->dropForeign(['collection_id']);
            $table->foreign('collection_id')->references('id')->on('collections')->nullOnDelete();
        });

        Schema::table('bookmark_tag', function (Blueprint $table): void {
            $table->dropForeign(['bookmark_id']);
            $table->dropForeign(['tag_id']);
            $table->foreign('bookmark_id')->references('id')->on('bookmarks')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookmark_tag', function (Blueprint $table): void {
            $table->dropForeign(['bookmark_id']);
            $table->dropForeign(['tag_id']);
            $table->foreign('bookmark_id')->references('id')->on('bookmarks');
            $table->foreign('tag_id')->references('id')->on('tags');
        });

        Schema::table('bookmarks', function (Blueprint $table): void {
            $table->dropForeign(['collection_id']);
            $table->foreign('collection_id')->references('id')->on('collections');
        });

        foreach (array_reverse($this->ownedTables) as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropConstrainedForeignId('user_id');
            });
        }
    }
};
