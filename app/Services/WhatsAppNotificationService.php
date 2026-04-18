<?php

namespace App\Services;

use App\Models\IplTransaction;
use App\Models\Resident;
use App\Models\ResidentDataUpdate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

/**
 * WhatsAppNotificationService
 *
 * Handles all WA bot notifications for RT 03 via Fonnte API:
 *  1. sendIplReceipt()    — IPL breakdown + Magic Link to the resident
 *  2. sendDataChangeAlert() — Notifies RT & Secretary when resident submits data update
 */
class WhatsAppNotificationService
{
    private string $token;
    private string $apiUrl;
    private string $rtWaNumber;
    private string $secretaryWaNumber;

    public function __construct()
    {
        $this->token            = config('services.fonnte.token', '');
        $this->apiUrl           = config('services.fonnte.api_url', 'https://api.fonnte.com/send');
        $this->rtWaNumber       = env('RT_WA_NUMBER', '');
        $this->secretaryWaNumber = env('SECRETARY_WA_NUMBER', '');
    }

    // ══════════════════════════════════════════════════════════════════════
    // 1. IPL RECEIPT + MAGIC LINK
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Send IPL payment receipt with breakdown details and magic link to resident.
     * Uses the exact format specified in the project requirements.
     */
    public function sendIplReceipt(
        Resident       $resident,
        IplTransaction $transaction,
        string         $magicLink,
        int            $year
    ): void {
        $monthName = Carbon::create()->month($transaction->month)->locale('id')->monthName;

        $message = $this->buildIplMessage($resident, $transaction, $monthName, $magicLink);

        $this->sendMessage($resident->wa_number, $message);

        logger()->info('[WA IPL Receipt Sent]', [
            'resident' => $resident->id,
            'month'    => $transaction->month,
            'year'     => $year,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════
    // 2. DATA CHANGE ALERT (to RT & Secretary)
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Alert RT & Secretary when a resident submits a data change request.
     */
    public function sendDataChangeAlert(Resident $resident, ResidentDataUpdate $update): void
    {
        $adminPortalUrl = url('/admin/data-requests');

        $message  = "🔔 *Permintaan Perubahan Data Warga – RT 03*\n\n";
        $message .= "Warga *{$resident->display_name}* mengajukan perubahan data:\n";

        if ($update->requested_name && $update->requested_name !== $resident->owner_name) {
            $message .= "• Nama: {$resident->owner_name} → *{$update->requested_name}*\n";
        }
        if ($update->requested_wa && $update->requested_wa !== $resident->wa_number) {
            $message .= "• Nomor WA: {$resident->wa_number} → *{$update->requested_wa}*\n";
        }
        if ($update->requested_family_members) {
            $message .= "• Data anggota keluarga diperbarui.\n";
        }
        if ($update->notes) {
            $message .= "• Catatan: {$update->notes}\n";
        }

        $message .= "\nSilakan tinjau di Admin Portal:\n{$adminPortalUrl}";

        // Send to RT number
        if ($this->rtWaNumber) {
            $this->sendMessage($this->rtWaNumber, $message);
        }

        // Send to Secretary number
        if ($this->secretaryWaNumber && $this->secretaryWaNumber !== $this->rtWaNumber) {
            $this->sendMessage($this->secretaryWaNumber, $message);
        }
    }

    // ══════════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Build the exact IPL message format as specified in requirements.
     */
    private function buildIplMessage(
        Resident       $resident,
        IplTransaction $tx,
        string         $monthName,
        string         $magicLink
    ): string {
        $rp = fn($n) => 'Rp ' . number_format((float)$n, 0, ',', '.');

        $total = (float)$tx->biaya_sampah
               + (float)$tx->biaya_keamanan
               + (float)$tx->kas_rt
               + (float)$tx->dana_sosial
               + (float)$tx->kas_rw;

        $lines = [];
        $lines[] = "Selamat pembayaran Anda telah diterima!";
        $lines[] = "Rincian Pembayaran IPL Bulan {$monthName}:";
        $lines[] = "- Sampah: {$rp($tx->biaya_sampah)}";
        $lines[] = "- Keamanan: {$rp($tx->biaya_keamanan)}";
        $lines[] = "- Masuk Kas RT: {$rp($tx->kas_rt)}";
        $lines[] = "- Masuk Dana Sosial: {$rp($tx->dana_sosial)}";
        $lines[] = "- Masuk Kas RW: {$rp($tx->kas_rw)}";
        $lines[] = "----------------------";
        $lines[] = "Total: {$rp($total)}";
        $lines[] = "";
        $lines[] = "Akses Kartu IPL Digital Anda melalui link berikut:";
        $lines[] = $magicLink;

        return implode("\n", $lines);
    }

    /**
     * Send a text-only WhatsApp message via Fonnte API.
     */
    private function sendMessage(string $target, string $message): void
    {
        if (empty($this->token)) {
            logger()->warning('[WA] FONNTE_TOKEN is not set. Message not sent.', compact('target'));
            return;
        }

        $response = Http::withHeaders(['Authorization' => $this->token])
            ->post($this->apiUrl, [
                'target'  => $target,
                'message' => $message,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException(
                'Fonnte API error (' . $response->status() . '): ' . $response->body()
            );
        }

        logger()->info('[WA] Message sent', ['target' => $target, 'status' => $response->status()]);
    }
}
