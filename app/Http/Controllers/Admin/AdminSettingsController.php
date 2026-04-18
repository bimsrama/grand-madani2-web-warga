<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoardMember;
use App\Models\CommunityWidget;
use App\Models\ResidentDataUpdate;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    // ══════════════════════════════════════════════════════════════════════
    // COMMUNITY WIDGETS (Homepage Cards)
    // ══════════════════════════════════════════════════════════════════════

    public function widgetIndex()
    {
        $widgets = CommunityWidget::where('rt_number', '3')->orderBy('sort_order')->get();
        return view('admin.settings.widgets', compact('widgets'));
    }

    public function widgetStore(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:100',
            'description'   => 'nullable|string|max:300',
            'thumbnail'     => 'nullable|image|max:3072',
            'external_link' => 'nullable|url|max:255',
            'sort_order'    => 'nullable|integer|min:0',
            'is_active'     => 'nullable|boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('widgets', 'public');
        }

        $data['rt_number'] = '3';
        $data['is_active'] = $request->boolean('is_active', true);

        CommunityWidget::create($data);
        return back()->with('success', 'Widget berhasil ditambahkan!');
    }

    public function widgetUpdate(Request $request, CommunityWidget $widget)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:100',
            'description'   => 'nullable|string|max:300',
            'thumbnail'     => 'nullable|image|max:3072',
            'external_link' => 'nullable|url|max:255',
            'sort_order'    => 'nullable|integer|min:0',
            'is_active'     => 'nullable|boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($widget->thumbnail_path) Storage::disk('public')->delete($widget->thumbnail_path);
            $data['thumbnail_path'] = $request->file('thumbnail')->store('widgets', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $widget->update($data);
        return back()->with('success', 'Widget berhasil diperbarui!');
    }

    public function widgetDestroy(CommunityWidget $widget)
    {
        if ($widget->thumbnail_path) Storage::disk('public')->delete($widget->thumbnail_path);
        $widget->delete();
        return back()->with('success', 'Widget dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // BOARD MEMBERS (Struktur Pengurus)
    // ══════════════════════════════════════════════════════════════════════

    public function boardIndex()
    {
        $members = BoardMember::ordered()->get();
        return view('admin.settings.board', compact('members'));
    }

    public function boardStore(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'role'       => 'required|string|max:100',
            'photo'      => 'nullable|image|max:3072',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('board', 'public');
        }

        $data['rt_number'] = '3';
        BoardMember::create($data);
        return back()->with('success', 'Anggota pengurus berhasil ditambahkan!');
    }

    public function boardUpdate(Request $request, BoardMember $member)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'role'       => 'required|string|max:100',
            'photo'      => 'nullable|image|max:3072',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('photo')) {
            if ($member->photo_path) Storage::disk('public')->delete($member->photo_path);
            $data['photo_path'] = $request->file('photo')->store('board', 'public');
        }

        $member->update($data);
        return back()->with('success', 'Data pengurus berhasil diperbarui!');
    }

    public function boardDestroy(BoardMember $member)
    {
        if ($member->photo_path) Storage::disk('public')->delete($member->photo_path);
        $member->delete();
        return back()->with('success', 'Anggota pengurus dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // WA SETTINGS (Fonnte API Config)
    // ══════════════════════════════════════════════════════════════════════

    public function waSettings()
    {
        return view('admin.settings.wa-settings', [
            'fonnteToken'    => env('FONNTE_TOKEN', ''),
            'rtWaNumber'     => env('RT_WA_NUMBER', ''),
            'secretaryWa'    => env('SECRETARY_WA_NUMBER', ''),
            'fonnteApiUrl'   => env('FONNTE_API_URL', 'https://api.fonnte.com/send'),
        ]);
    }

    public function saveWaSettings(Request $request)
    {
        $request->validate([
            'fonnte_token'    => 'nullable|string',
            'rt_wa_number'    => 'nullable|string',
            'secretary_wa'    => 'nullable|string',
        ]);

        // Update .env file safely
        $this->updateEnv([
            'FONNTE_TOKEN'         => $request->fonnte_token,
            'RT_WA_NUMBER'         => $request->rt_wa_number,
            'SECRETARY_WA_NUMBER'  => $request->secretary_wa,
        ]);

        return back()->with('success', 'Pengaturan WhatsApp Bot berhasil disimpan!');
    }

    // ══════════════════════════════════════════════════════════════════════
    // DATA WARGA MANAGEMENT
    // ══════════════════════════════════════════════════════════════════════

    public function residentIndex()
    {
        $residents = Resident::where('is_active', true)->orderBy('block')->orderBy('number')->get();
        return view('admin.residents.index', compact('residents'));
    }

    public function dataRequests()
    {
        $requests = ResidentDataUpdate::with('resident')
            ->where('rt_number', '3')
            ->orderBy('status')
            ->orderByDesc('created_at')
            ->get();
        return view('admin.residents.data-requests', compact('requests'));
    }

    public function approveRequest(Request $request, ResidentDataUpdate $update)
    {
        // Apply the approved changes to the actual resident record
        $resident = $update->resident;

        $updateData = [];
        if ($update->requested_name) $updateData['owner_name'] = $update->requested_name;
        if ($update->requested_wa)   $updateData['wa_number']  = $update->requested_wa;
        if ($update->requested_family_members) {
            $updateData['family_members'] = $update->requested_family_members;
        }

        if (! empty($updateData)) {
            $resident->update($updateData);
        }

        $update->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->user()->name,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Perubahan data untuk {$resident->owner_name} disetujui dan diterapkan.");
    }

    public function rejectRequest(Request $request, ResidentDataUpdate $update)
    {
        $update->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->user()->name,
            'reviewed_at' => now(),
        ]);

        return back()->with('info', 'Permintaan perubahan data ditolak.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════════

    private function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $value = str_replace('"', '', $value);
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $envContent
                );
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
