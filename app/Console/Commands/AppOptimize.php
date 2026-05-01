<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AppOptimize extends Command
{
    protected $signature   = 'app:optimize {--clear : Clear all caches instead of building}';
    protected $description = 'Cache config, routes, events và Filament (không cache views vì Livewire/Filament không tương thích)';

    public function handle(): int
    {
        if ($this->option('clear')) {
            return $this->clearAll();
        }

        return $this->buildAll();
    }

    private function buildAll(): int
    {
        $this->info('🚀 Đang tối ưu StayGo...');

        $steps = [
            ['config:cache',     'Config'],
            ['route:cache',      'Routes'],
            ['event:cache',      'Events'],
            ['filament:optimize','Filament components & icons'],
            ['icons:cache',      'Blade icons'],
        ];

        foreach ($steps as [$cmd, $label]) {
            $this->call($cmd);
        }

        $this->newLine();
        $this->info('✅ Tối ưu hoàn tất! (views không cache — an toàn cho Filament/Livewire)');

        return self::SUCCESS;
    }

    private function clearAll(): int
    {
        $this->info('🧹 Đang xóa cache StayGo...');

        $steps = [
            'config:clear',
            'route:clear',
            'event:clear',
            'view:clear',
            'cache:clear',
            'filament:optimize-clear',
            'icons:clear',
        ];

        foreach ($steps as $cmd) {
            $this->call($cmd);
        }

        $this->newLine();
        $this->info('✅ Đã xóa toàn bộ cache.');

        return self::SUCCESS;
    }
}
