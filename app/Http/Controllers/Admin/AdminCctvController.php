<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Models\Resident;
use Illuminate\Http\Request;

class AdminCctvController extends Controller
{
    /** List all cameras with resident-count badge */
    public function index()
    {
        $cameras = Camera::withCount('residents')->get();
        return view('admin.cctv.index', compact('cameras'));
    }

    /** Add a new camera */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'location'  => ['required', 'string', 'max:150'],
            'embed_url' => ['required', 'string'],
        ]);

        Camera::create($request->only(['name', 'location', 'embed_url']));

        return back()->with('success', 'Kamera berhasil ditambahkan!');
    }

    /** Show the access-control checklist for a camera */
    public function manage(Camera $camera)
    {
        $residents  = Resident::where('is_active', true)
            ->orderBy('block')->orderBy('number')
            ->get();
        $grantedIds = $camera->residents()->pluck('residents.id')->toArray();

        return view('admin.cctv.manage', compact('camera', 'residents', 'grantedIds'));
    }

    /**
     * Sync resident access for a camera.
     * Uses sync() to replace the whole pivot in one DB round-trip.
     */
    public function updateAccess(Request $request, Camera $camera)
    {
        $ids = array_filter((array) $request->input('resident_ids', []));
        $camera->residents()->sync($ids);

        return back()->with('success', "Akses CCTV \"{$camera->name}\" berhasil diperbarui!");
    }

    /** Toggle camera active/inactive */
    public function toggleActive(Camera $camera)
    {
        $camera->update(['is_active' => ! $camera->is_active]);
        $status = $camera->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Kamera \"{$camera->name}\" berhasil {$status}.");
    }

    /** Delete a camera (and its access records via cascade) */
    public function destroy(Camera $camera)
    {
        $camera->delete();
        return back()->with('success', 'Kamera berhasil dihapus.');
    }
}
