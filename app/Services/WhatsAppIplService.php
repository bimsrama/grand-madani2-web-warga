<?php

namespace App\Services;

use App\Models\IplTransaction;
use App\Models\Resident;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class WhatsAppIplService
{
    private string $token;
    private string $apiUrl;

    public function __construct()
    {
        $this->token  = config('services.fonnte.token', '');
        $this->apiUrl = 'https://api.fonnte.com/send';
    }

    /**
     * Full automation pipeline:
     *  1. Fetch all IPL transactions for the year
     *  2. Generate a dompdf invoice PDF
     *  3. Save PDF to storage/app/public/invoices
     *  4. Build a WhatsApp message with recap
     *  5. Send via Fonnte API (text + attached PDF file)
     */
    public function sendInvoice(Resident $resident, IplTransaction $transaction, int $year): void
    {
        // 1. Load the full year recap
        $yearTransactions = IplTransaction::where('resident_id', $resident->id)
            ->where('year', $year)
            ->get()
            ->keyBy('month');

        // 2. Generate & save PDF
        $pdfPath = $this->generateAndSavePdf($resident, $transaction, $yearTransactions, $year);

        // 3. Build WhatsApp text message
        $monthName = Carbon::create()->month($transaction->month)->locale('id')->monthName;
        $message   = $this->buildMessage($resident, $transaction, $yearTransactions, $monthName, $year);

        // 4. Send via Fonnte (multipart with PDF file attachment)
        $response = Http::withHeaders(['Authorization' => $this->token])
            ->attach('file', file_get_contents($pdfPath), basename($pdfPath))
            ->post($this->apiUrl, [
                'target'  => $resident->wa_number,
                'message' => $message,
                'type'    => 'file',
            ]);

        logger()->info('[WA Invoice Sent]', [
            'resident'  => $resident->id,
            'month'     => $transaction->month,
            'year'      => $year,
            'status'    => $response->status(),
        ]);

        if ($response->failed()) {
            throw new \RuntimeException(
                'Fonnte API error: ' . $response->body()
            );
        }
    }

    // ── Private Helpers ───────────────────────────────────────────────────

    private function generateAndSavePdf(
        Resident     $resident,
        IplTransaction $transaction,
        $yearTransactions,
        int          $year
    ): string {
        $pdf = Pdf::loadView('pdf.ipl-invoice', [
            'resident'         => $resident,
            'transaction'      => $transaction,
            'yearTransactions' => $yearTransactions,
            'year'             => $year,
        ])->setPaper('A4', 'portrait');

        $dir = storage_path('app/public/invoices');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = "ipl_{$resident->id}_{$transaction->month}_{$year}.pdf";
        $fullPath = "{$dir}/{$filename}";
        $pdf->save($fullPath);

        // Update invoice_path on the transaction record
        $transaction->update(['invoice_path' => "invoices/{$filename}"]);

        return $fullPath;
    }

    private function buildMessage(
        Resident       $resident,
        IplTransaction $transaction,
        $yearTransactions,
        string         $monthName,
        int            $year
    ): string {
        $amount = 'Rp ' . number_format($transaction->amount, 0, ',', '.');

        $lines   = [];
        $lines[] = "Assalamu'alaikum Wr. Wb. 🌿";
        $lines[] = "";
        $lines[] = "Yth. *{$resident->owner_name}*";
        $lines[] = "Blok {$resident->block} / No. {$resident->number}";
        $lines[] = "";
        $lines[] = "Alhamdulillah, pembayaran *IPL bulan {$monthName} {$year}* telah kami catat.";
        $lines[] = "💰 Nominal: *{$amount}*";
        $lines[] = "";
        $lines[] = "📋 *Rekap IPL Tahun {$year}:*";

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        for ($m = 1; $m <= 12; $m++) {
            $tx     = $yearTransactions->get($m);
            $status = ($tx && $tx->status === 'lunas') ? '✅ Lunas' : '❌ Belum';
            $lines[] = "{$monthNames[$m]}: {$status}";
        }

        $lines[] = "";
        $lines[] = "Terlampir bukti pembayaran dalam format PDF.";
        $lines[] = "Terima kasih atas partisipasi Bapak/Ibu dalam menjaga kebersihan dan keamanan lingkungan kita. 🏡";
        $lines[] = "";
        $lines[] = "Wassalamu'alaikum Wr. Wb.";
        $lines[] = "*Pengurus RT – Grand Madani 2*";

        return implode("\n", $lines);
    }
}
