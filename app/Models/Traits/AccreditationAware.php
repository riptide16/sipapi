<?php

namespace App\Models\Traits;

trait AccreditationAware
{
    /**
     * Formula: Jumlah Skor / (Jumlah Soal * 5) * Bobot
     */
    protected function calculateScore($count, $total, $weight)
    {
        return $total / ($count * 5) * $weight;
    }

    public function calculatePredicate($score)
    {
        if ($score >= 91) {
            return 'A';
        } elseif ($score >= 76) {
            return 'B';
        } elseif ($score >= 60) {
            return 'C';
        } else {
            return 'Tidak Akreditasi';
        }
    }

    /**
     * Format: <CurrentYear>/<Iteration>
     */
    public static function newCode()
    {
        $year = now()->format('Y');
        $last = self::where('code', 'like', "{$year}/%")
                    ->select(\DB::raw("CAST(REPLACE(code, '{$year}/', '') AS INTEGER) AS iteration"))
                    ->orderBy('iteration', 'desc')
                    ->first();

        $next = $last ? $last->iteration + 1 : 1;

        return "{$year}/{$next}";
    }
}
