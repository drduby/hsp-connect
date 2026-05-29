<?php

namespace Database\Seeders;

use App\Models\FaqItem;
use Illuminate\Database\Seeder;

class FaqItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'question' => 'Was gibt es zum Thema Spastik?',
                'question_en' => "What's available on the topic of Spasticity?",
                'answer' => 'Im Feed findest du viele Erfahrungen und Fragen rund um Spastik. Klicke auf den Tag # Spastik in der linken Sidebar oder im Hero-Bereich um alle Beiträge zu diesem Thema zu sehen.',
                'answer_en' => "In the feed you'll find many experiences and questions about spasticity. Click the # Spasticity tag in the left sidebar or the hero area to see all posts on this topic.",
                'tags' => ['spastik'],
            ],
            [
                'question' => 'Tipps zu Physiotherapie?',
                'question_en' => 'Tips on Physiotherapy?',
                'answer' => 'Unter dem Tag # Physiotherapie findest du Erfahrungsberichte und Fragen zur Physiotherapie bei Spastik. Viele Mitglieder teilen ihre Übungen und Empfehlungen.',
                'answer_en' => "Under the # Physiotherapy tag you'll find experience reports and questions about physiotherapy for spasticity. Many members share their exercises and recommendations.",
                'tags' => ['physiotherapie'],
            ],
            [
                'question' => 'Hilfsmittel für den Alltag?',
                'question_en' => 'Assistive Aids for Daily Life?',
                'answer' => 'Unter # Hilfsmittel und # Alltag teilen Mitglieder Empfehlungen für praktische Helfer. Von Greifhilfen bis zu speziellen Schuheinlagen.',
                'answer_en' => 'Under # Assistive Aids and # Daily Life, members share recommendations for practical helpers — from gripping aids to special shoe insoles.',
                'tags' => ['hilfsmittel', 'alltag'],
            ],
            [
                'question' => 'Entspannungstechniken bei Spastik?',
                'question_en' => 'Relaxation Techniques for Spasticity?',
                'answer' => 'Unter dem Tag # Entspannung findest du Erfahrungen mit verschiedenen Entspannungsmethoden — Wärme, Massage, Yoga und mehr.',
                'answer_en' => "Under the # Relaxation tag you'll find experiences with various relaxation methods — heat, massage, yoga and more.",
                'tags' => ['entspannung'],
            ],
            [
                'question' => 'Ernährung und Spastik?',
                'question_en' => 'Nutrition and Spasticity?',
                'answer' => 'Einige Mitglieder berichten unter # Ernährung über den Einfluss der Ernährung auf ihre Symptome.',
                'answer_en' => 'Some members report under # Nutrition about the influence of diet on their symptoms.',
                'tags' => ['ernährung'],
            ],
            [
                'question' => 'Schlafprobleme bei Spastik?',
                'question_en' => 'Sleep Problems with Spasticity?',
                'answer' => 'Unter # Schlaf tauschen sich Betroffene über Schlafprobleme und Lösungen aus — von Lagerungshilfen bis zu Abendroutinen.',
                'answer_en' => 'Under # Sleep, members exchange experiences about sleep problems and solutions — from positioning aids to evening routines.',
                'tags' => ['schlaf'],
            ],
            [
                'question' => 'Muskeln und Muskelspannung?',
                'question_en' => 'Muscles and Muscle Tension?',
                'answer' => 'Zum Thema Muskelspannung und -entspannung gibt es viele Beiträge unter dem Tag # Muskeln.',
                'answer_en' => 'On the topic of muscle tension and relaxation there are many posts under the # Muscles tag.',
                'tags' => ['muskeln'],
            ],
            [
                'question' => 'Tipps zur Rehabilitation bei Spastik?',
                'question_en' => 'Tips on Rehabilitation for Spasticity?',
                'answer' => 'Unter dem Tag # Reha tauschen sich Mitglieder über Rehabilitations-Erfahrungen aus — stationäre Reha, ambulante Therapien, und was wirklich hilft.',
                'answer_en' => 'Under the # Rehabilitation tag, members share rehabilitation experiences — inpatient rehab, outpatient therapies, and what really helps.',
                'tags' => ['reha'],
            ],
            [
                'question' => 'Was ist HSPConnect?',
                'question_en' => 'What is HSPConnect?',
                'answer' => 'Eine kostenlose Community für Menschen mit Hereditärer Spastischer Paraplegie (HSP) und anderen Formen von Spastik. Erfahrungen teilen, Fragen stellen, vernetzen.',
                'answer_en' => 'A free community for people with Hereditary Spastic Paraplegia (HSP) and other forms of spasticity. Share experiences, ask questions, connect.',
                'tags' => null,
            ],
            [
                'question' => 'Muss ich angemeldet sein um zu lesen?',
                'question_en' => 'Do I need to be logged in to read?',
                'answer' => 'Nein — alle Beiträge sind öffentlich lesbar. Für Kommentare, Likes und eigene Beiträge ist ein Konto erforderlich.',
                'answer_en' => 'No — all posts are publicly readable. An account is required for comments, likes and your own posts.',
                'tags' => null,
            ],
            [
                'question' => 'Ist HSPConnect kostenlos?',
                'question_en' => 'Is HSPConnect free?',
                'answer' => 'Ja, vollständig kostenlos. Kein Abo, keine versteckten Kosten.',
                'answer_en' => 'Yes, completely free. No subscription, no hidden costs.',
                'tags' => null,
            ],
            [
                'question' => 'Sind die Inhalte medizinisch geprüft?',
                'question_en' => 'Is the content medically reviewed?',
                'answer' => 'Nein. Alle Beiträge sind persönliche Erfahrungsberichte der Mitglieder. Sie ersetzen keinen Arztbesuch. Bei gesundheitlichen Fragen bitte immer medizinisches Fachpersonal aufsuchen.',
                'answer_en' => "No. All posts are personal experience reports from members. They do not replace a doctor's visit. For health questions, always consult medical professionals.",
                'tags' => null,
            ],
            [
                'question' => 'Wie melde ich einen problematischen Beitrag?',
                'question_en' => 'How do I report a problematic post?',
                'answer' => 'Nutze den "Melden"-Button direkt unter jedem Beitrag. Wir prüfen alle Meldungen und reagieren so schnell wie möglich.',
                'answer_en' => 'Use the "Report" button directly below each post. We review all reports and respond as quickly as possible.',
                'tags' => null,
            ],
            [
                'question' => 'Kann ich meinen Nickname später ändern?',
                'question_en' => 'Can I change my nickname later?',
                'answer' => 'Ja, nach dem Einloggen kannst du deinen Nickname in den Profileinstellungen anpassen.',
                'answer_en' => 'Yes, after logging in you can adjust your nickname in the profile settings.',
                'tags' => null,
            ],
            [
                'question' => 'Kann ich mein Konto löschen?',
                'question_en' => 'Can I delete my account?',
                'answer' => 'Ja. Schreib uns an hallo@hspconnect.at — wir löschen dein Konto und alle Daten innerhalb von 7 Tagen.',
                'answer_en' => 'Yes. Write to us at hallo@hspconnect.at — we will delete your account and all data within 7 days.',
                'tags' => null,
            ],
            [
                'question' => 'Wer betreibt HSPConnect?',
                'question_en' => 'Who operates HSPConnect?',
                'answer' => 'HSPConnect ist ein privates Community-Projekt von Betroffenen für Betroffene. Kein kommerzielles Unternehmen.',
                'answer_en' => 'HSPConnect is a private community project by affected people for affected people. No commercial company.',
                'tags' => null,
            ],
            [
                'question' => 'Welche Sprachen werden unterstützt?',
                'question_en' => 'Which languages are supported?',
                'answer' => 'Aktuell hauptsächlich Deutsch. Englische Beiträge sind willkommen.',
                'answer_en' => 'Currently primarily German. English posts are welcome.',
                'tags' => null,
            ],
            [
                'question' => 'Wie kann ich einen Beitrag speichern?',
                'question_en' => 'How can I save a post?',
                'answer' => 'Klicke auf das 🏷️-Symbol unter jedem Beitrag. Gespeicherte Beiträge findest du unter "Mein Bereich" in der linken Seitenleiste (nach dem Anmelden).',
                'answer_en' => 'Click the 🏷️ symbol below each post. Saved posts can be found under "My Area" in the left sidebar (after logging in).',
                'tags' => null,
            ],
        ];

        foreach ($items as $i => $item) {
            FaqItem::query()->updateOrCreate(
                ['question' => $item['question']],
                [
                    'question_en' => $item['question_en'],
                    'answer' => $item['answer'],
                    'answer_en' => $item['answer_en'],
                    'tags' => $item['tags'],
                    'sort_order' => $i,
                    'is_published' => true,
                ],
            );
        }
    }
}
