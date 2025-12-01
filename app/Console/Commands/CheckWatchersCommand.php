<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Watcher;
use App\Actions\CheckAvailabilityAction;

class CheckWatchersCommand extends Command
{
    protected $signature = 'watcher:check';
    protected $description = 'Vérifie les disponibilités pour tous les watchers';

    public function handle(CheckAvailabilityAction $action)
    {
        $watchers = Watcher::all();
        foreach ($watchers as $watcher) {
            $slots = $action->execute($watcher);

            if (!empty($slots)) {
                $this->info("⚠️ {$watcher->practitioner_name} ({$watcher->motive_name}) - créneaux disponibles : " . implode(', ', $slots));

                Notification::route('mail', config('doctolib.email'))
                    ->notify(new SlotAvailableNotification($watcher->practitioner_name, $watcher->motive_name, $slots));

                // Ajouter aux slots notifiés
                $watcher->notified_slots = array_merge($watcher->notified_slots ?? [], $slots);
                $watcher->save();
            } else {
                $this->info("{$watcher->practitioner_name} ({$watcher->motive_name}) - pas de créneaux disponibles");
            }
        }
    }
}

