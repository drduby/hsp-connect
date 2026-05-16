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
        Schema::table('post_ratings', function (Blueprint $table) {
            $table->foreignId('post_id')->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->after('post_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating')->unsigned()->after('user_id');

            $table->unique(['post_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('post_ratings', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['post_id', 'user_id', 'rating']);
        });
    }
};
