<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('post_id')->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->after('post_id')->constrained()->cascadeOnDelete();
            $table->text('content')->after('user_id');
            $table->foreignId('parent_id')->nullable()->after('content')->constrained('comments')->cascadeOnDelete();

            $table->index('post_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['post_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['parent_id', 'content', 'user_id', 'post_id']);
        });
    }
};
