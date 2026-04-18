<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Response;

class LetterGeneratorService
{
    /**
     * Build an official RT letter PDF with Kop Surat and return as download.
     */
    public function download(
        string $category,
        string $subject,
        string $content,
        string $date
    ): Response {
        $parsedDate   = Carbon::parse($date)->locale('id');
        $formattedDate = $parsedDate->isoFormat('D MMMM YYYY');
        $letterNumber  = $this->generateLetterNumber($parsedDate);

        $pdf = Pdf::loadView('pdf.letter', [
            'category'     => $category,
            'subject'      => $subject,
            'content'      => $content,
            'date'         => $formattedDate,
            'letterNumber' => $letterNumber,
        ])->setPaper('A4', 'portrait');

        $slug     = now()->format('Ymd-His');
        $filename = "Surat-RT-GM2-{$slug}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Generate an auto-incrementing letter number.
     * Format: 001/RT-GM2/IV/2025  (Roman numeral month)
     */
    private function generateLetterNumber(Carbon $date): string
    {
        $cacheKey = 'letter_seq_' . $date->format('Ym');
        $seq      = cache()->increment($cacheKey);   // atomic increment via Redis/file cache

        $romanMonth = $this->toRomanNumeral((int) $date->month);

        return sprintf('%03d/RT-GM2/%s/%d', $seq, $romanMonth, $date->year);
    }

    private function toRomanNumeral(int $month): string
    {
        $map = [
            1  => 'I',   2  => 'II',  3  => 'III', 4  => 'IV',
            5  => 'V',   6  => 'VI',  7  => 'VII', 8  => 'VIII',
            9  => 'IX',  10 => 'X',   11 => 'XI',  12 => 'XII',
        ];

        return $map[$month] ?? (string) $month;
    }
}
