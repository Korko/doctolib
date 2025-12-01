<?php

namespace App\Console\Commands;

use App\Models\Watcher;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use function Laravel\Prompts\{text, search, select};

class AddWatcherCommand extends Command
{
    protected $signature = 'watcher:add';
    protected $description = 'Ajouter un spécialiste à surveiller via Doctolib';

    public function handle()
    {
        // 1. Nom du spécialiste
        $practitioner = $this->askPractitioner();

        // 2. Choix du motif
        $motive = $this->askMotive($practitioner);

        // 3. Récupérer l'agenda
        $agenda = $this->askAgenda($practitioner, $motive);

        // 6. Demande des dates
        $start = text(
            label: 'Date de début',
            required: true,
//            validate: ['date' => 'required|date|date_format:Y-m-d H:i']
        );
        $end = text(
            label: 'Date de fin',
            required: true,
//            validate: ['date' => 'required|date|date_format:Y-m-d H:i']
        );

        // 7. Stocker le watcher
        $watcher = Watcher::create([
            'practitioner_name' => $practitioner['name_with_title'],
            'agenda_id' => $agenda['id'],
            'motive_id' => $motive['id'],
            'motive_name' => $motive['name'],
            'starts_at' => $start,
            'ends_at' => $end,
        ]);

        $this->info("Watcher créé : {$watcher->id} pour {$watcher->practitioner_name} ({$watcher->motive_name})");
    }

    private function askPractitioner() : array
    {
        $link = search(
            label: 'Nom du spécialiste à surveiller',
            options: function (string $name) {
                if (strlen($name) < 3)
                    return [];

                return $this->getPractitionersList($name);
            }
        );

        return $this->getPractitioner($link);
    }

    private function getPractitionersList(string $name) : array
    {
        $searchResponse = Http::get('https://www.doctolib.fr/api/searchbar/autocomplete.json', [
            'search' => $name,
        ]);

        $practitioners = $searchResponse->json()['profiles'] ?? [];
        return Arr::mapWithKeys($practitioners, fn($p) => [$p['link'] => $p['name'] . ' - ' . $p['city']]);
    }

    private function getPractitioner(string $link) : array
    {
        $profileResponse = Http::get("https://www.doctolib.fr/{$link}.json");
        $profileData = $profileResponse->json();
        return $profileData['data']['profile'];
    }

    private function askMotive(array $practitioner) : array
    {
        $profileResponse = Http::get(
            'https://www.doctolib.fr/online_booking/api/slot_selection_funnel/v1/info.json',
            [
                'profile_slug' => $practitioner['slug']
            ]
        );

        $profileData = $profileResponse->json();

        $specialities = $profileData['data']['specialities'] ?? [];
        $motivesCategories = $profileData['data']['visit_motive_categories'] ?? [];
        $visitMotives = $profileData['data']['visit_motives'] ?? [];

        if (empty($visitMotives)) {
            $this->error("Impossible de récupérer les motifs.");
            exit(1);
        }

        $specialities = array_column($specialities, 'name', 'id');
        $categories = array_column($motivesCategories, 'name', 'id');
        $motives = array_column($visitMotives, null, 'id');

        $reason = select(
            label: 'Choisissez le motif',
            options: Arr::mapWithKeys(
                $motives, fn($m) => [
                    $m['id'] => $specialities[$m['speciality_id']] . ' - ' . $categories[$m['visit_motive_category_id']] . ' - ' . $m['name']
                ]
            ),
            scroll: 10
        );

        return $motives[$reason];
    }

    private function askAgenda(array $practitioner, array $motive) : array
    {
        $profileResponse = Http::get(
            'https://www.doctolib.fr/online_booking/api/slot_selection_funnel/v1/info.json',
            [
                'profile_slug' => $practitioner['slug']
            ]
        );

        $profileData = $profileResponse->json();

        $agendas = $profileData['data']['agendas'] ?? [];

        if (empty($agendas)) {
            $this->error("Impossible de récupérer les agendas.");
            exit(2);
        }

        // 5. Choix de l'agenda correspondant au motif (souvent le premier)
        return Arr::first($agendas, fn (array $a) => in_array($motive['id'], $a['visit_motive_ids'] ?? []));
    }
}
