<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetDemoData extends Command
{
    protected $signature = 'app:reset-content {--force : Skip confirmation prompt}';

    protected $description = 'Truncate all user-generated content. Keeps tags, FAQ, and admin users.';

    public function handle(): int
    {
        if (! $this->option('force')) {
            $this->warn('This will permanently delete ALL posts, comments, users (non-admin), and logs.');
            if (! $this->confirm('Are you sure you want to continue?')) {
                $this->info('Aborted.');

                return self::SUCCESS;
            }
        }

        $this->info('Resetting content...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $truncate = [
            'post_tag',
            'post_likes',
            'post_ratings',
            'post_saves',
            'post_reports',
            'comments',
            'posts',
            'feedbacks',
            'activity_logs',
            'notifications',
            'passkeys',
            'password_reset_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'failed_jobs',
            'jobs',
            'job_batches',
        ];

        foreach ($truncate as $table) {
            DB::table($table)->truncate();
            $this->line("  Cleared: {$table}");
        }

        $deleted = DB::table('users')->where('is_admin', false)->delete();
        $this->line("  Deleted: {$deleted} non-admin user(s)");

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            $this->line('  Cleared: storage/logs/laravel.log');
        }

        $this->newLine();
        $this->info('Done. Tags, FAQ, and admin accounts are untouched.');

        return self::SUCCESS;
    }
}
