<?php

namespace App\Console\Commands;

use App\Models\Watcher;
use Illuminate\Console\Command;

class CleanDeprecatedWatchersCommand extends Command
{
    protected $signature = 'watcher:clean';
    protected $description = 'Supprime les watchers dépassés';

    public function handle()
    {
        Watcher::wherePast('ends_at')->delete();
    }
}
