<?php

namespace App\Http\Controllers;

use App\Models\Resident;

class ResidentCctvController extends Controller
{
    public function index()
    {
        $resident = Resident::with(['cameras' => function ($q) {
            $q->where('is_active', true);
        }])->findOrFail(session('resident_id'));

        // Only cameras explicitly granted by admin via camera_resident pivot
        $cameras = $resident->cameras;

        return view('resident.cctv', compact('resident', 'cameras'));
    }
}
