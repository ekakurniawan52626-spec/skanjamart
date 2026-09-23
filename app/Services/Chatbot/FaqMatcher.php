<?php

namespace App\Services\Chatbot;

/**
 * Mencocokkan pertanyaan pelanggan dengan daftar FAQ.
 * Kata langka (mis. "resi") bobotnya lebih besar daripada kata yang ada di banyak FAQ (mis. "pesan").
 */
class FaqMatcher
{
    public const STRONG = 0.55;
    public const WEAK = 0.30;

    /**
     * @param  iterable<object|array>  $faqs  punya question, keywords, dan id
     * @return array<int, array{faq: mixed, score: float}> urut dari skor tertinggi
     */
    public function rank(iterable $faqs, string $query): array
    {
        $queryTokens = Text::tokens($query);

        if ($queryTokens === []) {
            return [];
        }

        $docs = [];
        foreach ($faqs as $faq) {
            $question = (string) data_get($faq, 'question', '');
            $keywords = (string) data_get($faq, 'keywords', '');

            $docs[] = [
                'faq' => $faq,
                'tokens' => Text::tokens($question . ' ' . $keywords),
                'question' => implode(' ', Text::tokens($question)),
            ];
        }

        if ($docs === []) {
            return [];
        }

        // Berapa banyak FAQ yang memuat tiap kata -> dasar bobot (IDF).
        $docFreq = [];
        foreach ($docs as $doc) {
            foreach ($doc['tokens'] as $token) {
                $docFreq[$token] = ($docFreq[$token] ?? 0) + 1;
            }
        }

        $total = count($docs);
        $idf = fn (string $t) => log(1 + $total / (1 + ($docFreq[$t] ?? 0)));
        $unknownWeight = $idf('__tidak_ada__') * 0.5;

        $queryWeight = 0.0;
        foreach ($queryTokens as $token) {
            $queryWeight += isset($docFreq[$token]) ? $idf($token) : $unknownWeight;
        }

        $normalizedQuery = implode(' ', $queryTokens);
        $results = [];

        foreach ($docs as $doc) {
            $matched = array_intersect($queryTokens, $doc['tokens']);

            if ($matched === []) {
                continue;
            }

            $matchedWeight = 0.0;
            foreach ($matched as $token) {
                $matchedWeight += $idf($token);
            }

            $coverage = $queryWeight > 0 ? $matchedWeight / $queryWeight : 0.0;
            $tightness = count($matched) / max(1, count($doc['tokens']));
            $score = $coverage + 0.05 * $tightness;

            // Pertanyaan persis sama (mis. dari tombol saran) -> pasti menang.
            if ($normalizedQuery !== '' && $normalizedQuery === $doc['question']) {
                $score += 1.0;
            }

            $results[] = ['faq' => $doc['faq'], 'score' => $score];
        }

        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);

        return $results;
    }
}
