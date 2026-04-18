<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IplTransaction;
use App\Models\Resident;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class AdminIplController extends Controller
{
    public function __construct(private WhatsAppNotificationService $waService) {}

    public function index(Request $request)
    {
        $year      = (int) $request->get('year', now()->year);
        $residents = Resident::where('is_active', true)
            ->orderBy('block')
            ->orderBy('number')
            ->get();

        // Load all IPL transactions for the year, grouped by resident_id → month
        $rawTx = IplTransaction::where('year', $year)
            ->get()
            ->groupBy('resident_id')
            ->map(fn ($group) => $group->keyBy('month'));

        return view('admin.ipl.index', compact('residents', 'rawTx', 'year'));
    }

    /**
     * Update IPL status for a specific house/month.
     * If marked "lunas":
     *   1. Save all breakdown amounts
     *   2. Generate a signed Magic Link URL
     *   3. Send WA notification with breakdown + magic link
     */
    public function update(Request $request, Resident $resident, int $month, int $year)
    {
        $data = $request->validate([
            'status'          => ['required', 'in:lunas,belum'],
            'biaya_sampah'    => ['required_if:status,lunas', 'numeric', 'min:0'],
            'biaya_keamanan'  => ['required_if:status,lunas', 'numeric', 'min:0'],
            'kas_rt'          => ['required_if:status,lunas', 'numeric', 'min:0'],
            'dana_sosial'     => ['required_if:status,lunas', 'numeric', 'min:0'],
            'kas_rw'          => ['required_if:status,lunas', 'numeric', 'min:0'],
        ]);

        $totalAmount = ($data['biaya_sampah'] ?? 0)
                     + ($data['biaya_keamanan'] ?? 0)
                     + ($data['kas_rt'] ?? 0)
                     + ($data['dana_sosial'] ?? 0)
                     + ($data['kas_rw'] ?? 0);

        $transaction = IplTransaction::updateOrCreate(
            ['resident_id' => $resident->id, 'month' => $month, 'year' => $year],
            [
                'rt_number'       => '3',
                'amount'          => $totalAmount,
                'biaya_sampah'    => $data['biaya_sampah'] ?? 0,
                'biaya_keamanan'  => $data['biaya_keamanan'] ?? 0,
                'kas_rt'          => $data['kas_rt'] ?? 0,
                'dana_sosial'     => $data['dana_sosial'] ?? 0,
                'kas_rw'          => $data['kas_rw'] ?? 0,
                'status'          => $data['status'],
                'paid_at'         => $data['status'] === 'lunas' ? now() : null,
            ]
        );

        // Generate Magic Link for resident to view their IPL card without logging in
        if ($data['status'] === 'lunas') {
            $magicLink = URL::signedRoute('resident.ipl.magic', [
                'resident' => $resident->id,
                'month'    => $month,
                'year'     => $year,
            ], now()->addDays(30));

            // Store a short token reference
            $transaction->update(['magic_link_token' => substr(md5($magicLink), 0, 16)]);

            try {
                $this->waService->sendIplReceipt($resident, $transaction, $magicLink, $year);
            } catch (\Throwable $e) {
                logger()->error('[WA IPL] ' . $e->getMessage(), [
                    'resident_id' => $resident->id,
                    'month'       => $month,
                    'year'        => $year,
                ]);
                return back()->with('warning',
                    'Status IPL diperbarui, tapi pengiriman WhatsApp gagal: ' . $e->getMessage()
                );
            }
        }

        return back()->with('success',
            "IPL {$resident->display_name} bulan {$month}/{$year} berhasil diperbarui."
        );
    }
}
