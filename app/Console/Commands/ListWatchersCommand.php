<?php

namespace App\Console\Commands;

use App\Models\Watcher;
use Illuminate\Console\Command;

class ListWatchersCommand extends Command
{
    protected $signature = 'watcher:list';
    protected $description = 'Liste les watchers en cours';

    public function handle()
    {
        $this->table(
            ['Name', 'Motive', 'Period start', 'Period end'],
            Watcher::all(['practitioner_name', 'motive_name', 'starts_at', 'ends_at'])->toArray()
        );
    }
}
