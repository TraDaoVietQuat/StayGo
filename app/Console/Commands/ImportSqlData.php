<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSqlData extends Command
{
    protected $signature = 'db:import {file : Đường dẫn tới file SQL}';
    protected $description = 'Import chỉ các câu INSERT từ file SQL (bỏ qua CREATE TABLE, ALTER TABLE...)';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("Không tìm thấy file: $file");
            return 1;
        }

        $sql = file_get_contents($file);

        // Lấy tất cả câu INSERT INTO (kể cả multiline)
        preg_match_all('/^INSERT INTO\s.+?;$/ms', $sql, $matches);

        if (empty($matches[0])) {
            $this->error('Không tìm thấy câu INSERT nào trong file.');
            return 1;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $success = 0;
        $failed  = 0;

        foreach ($matches[0] as $statement) {
            try {
                DB::unprepared($statement);
                $success++;
            } catch (\Throwable $e) {
                $failed++;
                $this->warn('Lỗi: ' . $e->getMessage());
                $this->warn('→ ' . mb_substr(trim($statement), 0, 100) . '...');
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info("Hoàn thành! Thành công: {$success} | Lỗi: {$failed}");
        return 0;
    }
}
