<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Complaint;
use Illuminate\Http\Request;

class PublicComplaintController extends Controller
{
    public function index(Request $request)
    {
        $rt = $request->get('rt', '1');
        
        $announcements = \App\Models\Announcement::withoutGlobalScopes()
            ->where('rt_number', $rt)
            ->where('is_published', true)
            ->latest()
            ->get();
            
        $complaints = \App\Models\Complaint::withoutGlobalScopes()
            ->where('rt_number', $rt)
            ->where('is_public', true)
            ->latest()
            ->get();

        return view('public.aduan', compact('announcements', 'complaints', 'rt'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reporter_name'    => ['required', 'string', 'max:100'],
            'reporter_address' => ['required', 'string', 'max:200'],
            'category'         => ['required', 'string', 'max:100'],
            'description'      => ['required', 'string', 'min:20'],
        ], [
            'description.min' => 'Deskripsi aduan minimal 20 karakter.',
        ]);

        Complaint::create($request->only([
            'reporter_name', 'reporter_address', 'category', 'description',
        ]));

        return back()->with('success', 'Aduan Anda berhasil dikirim! Tim RT akan segera menindaklanjuti.');
    }
}
