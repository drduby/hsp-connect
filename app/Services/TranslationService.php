<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TranslationService
{
    public function translate(string $text, string $targetLang, string $sourceLang = 'auto'): ?string
    {
        if (trim($text) === '') {
            return null;
        }

        return config('services.deepl.key')
            ? $this->deepl($text, $targetLang, $sourceLang)
            : $this->myMemory($text, $targetLang, $sourceLang === 'auto' ? 'de' : $sourceLang);
    }

    private function deepl(string $text, string $targetLang, string $sourceLang): ?string
    {
        $base = str_ends_with(config('services.deepl.key'), ':fx')
            ? 'https://api-free.deepl.com'
            : 'https://api.deepl.com';

        $response = Http::withHeaders(['Authorization' => 'DeepL-Auth-Key '.config('services.deepl.key')])
            ->post($base.'/v2/translate', [
                'text' => [$text],
                'target_lang' => strtoupper($targetLang),
                'source_lang' => $sourceLang !== 'auto' ? strtoupper($sourceLang) : null,
            ]);

        return $response->ok() ? $response->json('translations.0.text') : null;
    }

    private function myMemory(string $text, string $targetLang, string $sourceLang): ?string
    {
        $chunks = $this->splitText($text, 500);
        $translated = [];

        foreach ($chunks as $chunk) {
            $response = Http::get('https://api.mymemory.translated.net/get', [
                'q' => $chunk,
                'langpair' => $sourceLang.'|'.$targetLang,
            ]);

            if (! $response->ok()) {
                return null;
            }

            $translated[] = $response->json('responseData.translatedText');
        }

        return implode(' ', $translated);
    }

    private function splitText(string $text, int $maxLen): array
    {
        if (mb_strlen($text) <= $maxLen) {
            return [$text];
        }

        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [$text];
        $chunks = [];
        $current = '';

        foreach ($sentences as $sentence) {
            if (mb_strlen($current) + mb_strlen($sentence) + 1 <= $maxLen) {
                $current .= ($current ? ' ' : '').$sentence;
            } else {
                if ($current) {
                    $chunks[] = $current;
                }
                $current = mb_strlen($sentence) > $maxLen
                    ? mb_substr($sentence, 0, $maxLen)
                    : $sentence;
            }
        }

        if ($current) {
            $chunks[] = $current;
        }

        return $chunks;
    }
}
