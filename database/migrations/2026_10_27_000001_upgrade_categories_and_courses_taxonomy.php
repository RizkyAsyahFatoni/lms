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
        // 1. Upgrade categories table (add parent_id and tags)
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('categories', 'tags')) {
                $table->text('tags')->nullable()->after('description'); // Suggestions for sub-sub categories
            }
        });

        // 2. Upgrade courses table (add sub_category_id and tags)
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'sub_category_id')) {
                $table->foreignId('sub_category_id')->nullable()->after('category_id')->constrained('categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('courses', 'tags')) {
                $table->text('tags')->nullable()->after('sub_category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'tags')) {
                $table->dropColumn('tags');
            }
            if (Schema::hasColumn('courses', 'sub_category_id')) {
                $table->dropForeign(['sub_category_id']);
                $table->dropColumn('sub_category_id');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'tags')) {
                $table->dropColumn('tags');
            }
            if (Schema::hasColumn('categories', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
        });
    }
};
