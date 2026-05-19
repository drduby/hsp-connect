<?php

namespace Database\Seeders;

use App\Models\FaqItem;
use Illuminate\Database\Seeder;

class FaqItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['question' => 'Was gibt es zum Thema Spastik?', 'answer' => 'Im Feed findest du viele Erfahrungen und Fragen rund um Spastik. Klicke auf den Tag # Spastik in der linken Sidebar oder im Hero-Bereich um alle Beiträge zu diesem Thema zu sehen.', 'tags' => ['spastik']],
            ['question' => 'Tipps zu Physiotherapie?', 'answer' => 'Unter dem Tag # Physiotherapie findest du Erfahrungsberichte und Fragen zur Physiotherapie bei Spastik. Viele Mitglieder teilen ihre Übungen und Empfehlungen.', 'tags' => ['physiotherapie']],
            ['question' => 'Hilfsmittel für den Alltag?', 'answer' => 'Unter # Hilfsmittel und # Alltag teilen Mitglieder Empfehlungen für praktische Helfer. Von Greifhilfen bis zu speziellen Schuheinlagen.', 'tags' => ['hilfsmittel', 'alltag']],
            ['question' => 'Entspannungstechniken bei Spastik?', 'answer' => 'Unter dem Tag # Entspannung findest du Erfahrungen mit verschiedenen Entspannungsmethoden — Wärme, Massage, Yoga und mehr.', 'tags' => ['entspannung']],
            ['question' => 'Ernährung und Spastik?', 'answer' => 'Einige Mitglieder berichten unter # Ernährung über den Einfluss der Ernährung auf ihre Symptome.', 'tags' => ['ernährung']],
            ['question' => 'Schlafprobleme bei Spastik?', 'answer' => 'Unter # Schlaf tauschen sich Betroffene über Schlafprobleme und Lösungen aus — von Lagerungshilfen bis zu Abendroutinen.', 'tags' => ['schlaf']],
            ['question' => 'Muskeln und Muskelspannung?', 'answer' => 'Zum Thema Muskelspannung und -entspannung gibt es viele Beiträge unter dem Tag # Muskeln.', 'tags' => ['muskeln']],
            ['question' => 'Tipps zur Rehabilitation bei Spastik?', 'answer' => 'Unter dem Tag # Reha tauschen sich Mitglieder über Rehabilitations-Erfahrungen aus — stationäre Reha, ambulante Therapien, und was wirklich hilft.', 'tags' => ['reha']],
            ['question' => 'Was ist HSPConnect?', 'answer' => 'Eine kostenlose Community für Menschen mit Hereditärer Spastischer Paraplegie (HSP) und anderen Formen von Spastik. Erfahrungen teilen, Fragen stellen, vernetzen.'],
            ['question' => 'Muss ich angemeldet sein um zu lesen?', 'answer' => 'Nein — alle Beiträge sind öffentlich lesbar. Für Kommentare, Likes und eigene Beiträge ist ein Konto erforderlich.'],
            ['question' => 'Ist HSPConnect kostenlos?', 'answer' => 'Ja, vollständig kostenlos. Kein Abo, keine versteckten Kosten.'],
            ['question' => 'Sind die Inhalte medizinisch geprüft?', 'answer' => 'Nein. Alle Beiträge sind persönliche Erfahrungsberichte der Mitglieder. Sie ersetzen keinen Arztbesuch. Bei gesundheitlichen Fragen bitte immer medizinisches Fachpersonal aufsuchen.'],
            ['question' => 'Wie melde ich einen problematischen Beitrag?', 'answer' => 'Nutze den "Melden"-Button direkt unter jedem Beitrag. Wir prüfen alle Meldungen und reagieren so schnell wie möglich.'],
            ['question' => 'Kann ich meinen Nickname später ändern?', 'answer' => 'Ja, nach dem Einloggen kannst du deinen Nickname in den Profileinstellungen anpassen.'],
            ['question' => 'Kann ich mein Konto löschen?', 'answer' => 'Ja. Schreib uns an hallo@hspconnect.at — wir löschen dein Konto und alle Daten innerhalb von 7 Tagen.'],
            ['question' => 'Wer betreibt HSPConnect?', 'answer' => 'HSPConnect ist ein privates Community-Projekt von Betroffenen für Betroffene. Kein kommerzielles Unternehmen.'],
            ['question' => 'Welche Sprachen werden unterstützt?', 'answer' => 'Aktuell hauptsächlich Deutsch. Englische Beiträge sind willkommen.'],
            ['question' => 'Wie kann ich einen Beitrag speichern?', 'answer' => 'Klicke auf das 🏷️-Symbol unter jedem Beitrag. Gespeicherte Beiträge findest du unter "Mein Bereich" in der linken Seitenleiste (nach dem Anmelden).'],
        ];

        foreach ($items as $i => $item) {
            FaqItem::create([
                'question' => $item['question'],
                'answer' => $item['answer'],
                'tags' => $item['tags'] ?? null,
                'sort_order' => $i,
                'is_published' => true,
            ]);
        }
    }
}
