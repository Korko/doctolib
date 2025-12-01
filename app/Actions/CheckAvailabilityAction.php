<?php

namespace App\Actions;

use App\Models\Watcher;
use Illuminate\Support\Facades\Http;

class CheckAvailabilityAction
{
    public function execute(Watcher $watcher): array
    {
        $url = 'https://www.doctolib.fr/availabilities.json';
        $response = Http::get($url, [
            'start_date' => $watcher->starts_at->format('Y-m-d'),
            'end_date' => $watcher->ends_at->format('Y-m-d'),
            'agenda_ids[]' => $watcher->agenda_id,
            'visit_motive_ids[]' => $watcher->motive_id,
        ]);

        $data = $response->json();
        $availableSlots = [];

        foreach ($data['availabilities'] ?? [] as $day) {
            foreach ($day['slots'] ?? [] as $slot) {
                if (!in_array($slot, $watcher->notified_slots ?? [])) {
                    $availableSlots[] = $slot;
                }
            }
        }

        return $availableSlots;
    }
}
