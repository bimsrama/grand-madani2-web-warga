<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Models\ResidentDataUpdate;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class ResidentAuthController extends Controller
{
    // ══════════════════════════════════════════════════════════════════════
    // LOGIN FLOW — Step 1: Show form
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Show the resident login form.
     * Hardcoded to RT 03. Dropdown lists all active RT 03 residents.
     */
    public function showLogin(): View
    {
        // RT 03 strict: withoutGlobalScopes bypassed since BelongsToRT public=RT3
        $residents = Resident::where('is_active', true)
            ->orderBy('block')
            ->orderBy('number')
            ->get();

        return view('resident.login', compact('residents'));
    }

    // ══════════════════════════════════════════════════════════════════════
    // AJAX — Step 2: Return masked WA number for selected resident
    // ══════════════════════════════════════════════════════════════════════

    public function getWaMask(Request $request): JsonResponse
    {
        $request->validate([
            'block'  => 'required|string',
            'number' => 'required|string',
        ]);

        $resident = Resident::where('block', $request->block)
            ->where('number', $request->number)
            ->where('is_active', true)
            ->first();

        if (! $resident) {
            return response()->json(['error' => 'Rumah tidak ditemukan.'], 404);
        }

        return response()->json([
            'masked_wa'  => $resident->masked_wa,
            'has_pin'    => $resident->hasPinSet(),
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════
    // LOGIN — Step 3: Authenticate (WA last-4 OR 6-digit PIN)
    // ══════════════════════════════════════════════════════════════════════

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'block'    => 'required|string',
            'number'   => 'required|string',
            'password' => 'required|string',
        ]);

        $resident = Resident::where('block', $request->block)
            ->where('number', $request->number)
            ->where('is_active', true)
            ->first();

        if (! $resident) {
            return back()->withErrors(['password' => 'Data rumah tidak ditemukan.'])->withInput();
        }

        // ── Branch A: PIN not set → verify last 4 digits of WA ────────
        if (! $resident->hasPinSet()) {
            if (! $resident->verifyWaLastDigits($request->password)) {
                return back()->withErrors([
                    'password' => 'Nomor WA terakhir 4 digit tidak cocok.',
                ])->withInput();
            }

            // Temporarily store resident ID in session for set-pin page
            session(['pending_pin_resident_id' => $resident->id]);

            return redirect()->route('resident.set-pin')
                ->with('info', 'Selamat datang! Silakan buat PIN 6 digit Anda untuk login berikutnya.');
        }

        // ── Branch B: PIN is set → verify 6-digit PIN ─────────────────
        if (! $resident->verifyPin($request->password)) {
            return back()->withErrors([
                'password' => 'PIN 6 digit tidak cocok. Silakan coba lagi.',
            ])->withInput();
        }

        $this->loginResident($resident, $request);

        return redirect()->route('resident.portal')
            ->with('success', "Selamat datang kembali, {$resident->owner_name}!");
    }

    // ══════════════════════════════════════════════════════════════════════
    // SET PIN — Steps for first-time residents
    // ══════════════════════════════════════════════════════════════════════

    public function showSetPin(): View|RedirectResponse
    {
        if (! session('pending_pin_resident_id')) {
            return redirect()->route('resident.login')
                ->with('error', 'Sesi tidak valid. Silakan login ulang.');
        }

        return view('resident.set-pin');
    }

    public function storePin(Request $request): RedirectResponse
    {
        $request->validate([
            'pin'              => 'required|string|size:6|regex:/^[0-9]{6}$/',
            'pin_confirmation' => 'required|same:pin',
        ], [
            'pin.size'    => 'PIN harus tepat 6 digit angka.',
            'pin.regex'   => 'PIN hanya boleh berisi angka.',
            'pin_confirmation.same' => 'Konfirmasi PIN tidak cocok.',
        ]);

        $residentId = session('pending_pin_resident_id');
        if (! $residentId) {
            return redirect()->route('resident.login')
                ->with('error', 'Sesi habis. Silakan login ulang.');
        }

        $resident = Resident::findOrFail($residentId);
        $resident->setNewPin($request->pin);

        // Clear pending session, establish full session
        session()->forget('pending_pin_resident_id');
        $this->loginResident($resident, $request);

        return redirect()->route('resident.portal')
            ->with('success', "PIN berhasil dibuat. Selamat datang, {$resident->owner_name}!");
    }

    // ══════════════════════════════════════════════════════════════════════
    // RESIDENT PORTAL — Dashboard
    // ══════════════════════════════════════════════════════════════════════

    public function showPortal(): View
    {
        $resident = Resident::findOrFail(session('resident_id'));

        $pendingUpdate = $resident->dataUpdateRequests()
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('resident.portal', compact('resident', 'pendingUpdate'));
    }

    // ══════════════════════════════════════════════════════════════════════
    // DATA UPDATE REQUEST — Perubahan Data Warga
    // ══════════════════════════════════════════════════════════════════════

    public function showUpdateData(): View
    {
        $resident = Resident::findOrFail(session('resident_id'));
        return view('resident.update-data', compact('resident'));
    }

    public function submitUpdateData(Request $request, WhatsAppNotificationService $waService): RedirectResponse
    {
        $request->validate([
            'requested_name' => 'nullable|string|max:100',
            'requested_wa'   => 'nullable|string|max:20',
            'notes'          => 'nullable|string|max:500',
        ]);

        $resident = Resident::findOrFail(session('resident_id'));

        // Parse family members from request
        $familyMembers = [];
        if ($request->filled('family_name') && is_array($request->family_name)) {
            foreach ($request->family_name as $i => $name) {
                if (! empty($name)) {
                    $familyMembers[] = [
                        'name'     => $name,
                        'relation' => $request->family_relation[$i] ?? '',
                    ];
                }
            }
        }

        $update = ResidentDataUpdate::create([
            'resident_id'               => $resident->id,
            'rt_number'                 => '3',
            'requested_name'            => $request->requested_name ?: $resident->owner_name,
            'requested_wa'              => $request->requested_wa ?: $resident->wa_number,
            'requested_family_members'  => $familyMembers ?: ($resident->family_members ?? []),
            'notes'                     => $request->notes,
            'status'                    => 'pending',
        ]);

        // Send WA notification to RT & Secretary
        try {
            $waService->sendDataChangeAlert($resident, $update);
        } catch (\Throwable $e) {
            logger()->error('[WA DataChange] ' . $e->getMessage());
        }

        return redirect()->route('resident.portal')
            ->with('success', 'Permintaan perubahan data berhasil dikirim! Pengurus RT akan segera meninjau.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // LOGOUT
    // ══════════════════════════════════════════════════════════════════════

    public function logout(Request $request): RedirectResponse
    {
        session()->forget([
            'resident_id', 'resident_rt', 'resident_name',
            'resident_block', 'resident_number', 'pending_pin_resident_id',
        ]);

        $request->session()->regenerate();

        return redirect()->route('resident.login')
            ->with('info', 'Anda telah keluar dari portal warga RT 03.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════════

    private function loginResident(Resident $resident, Request $request): void
    {
        session([
            'resident_id'     => $resident->id,
            'resident_rt'     => '3',
            'resident_name'   => $resident->owner_name,
            'resident_block'  => $resident->block,
            'resident_number' => $resident->number,
        ]);

        $request->session()->regenerate();
    }
}
