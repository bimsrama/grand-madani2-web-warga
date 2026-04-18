<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LetterTemplate;
use App\Services\LetterGeneratorService;
use Illuminate\Http\Request;

class AdminSecretaryController extends Controller
{
    public function __construct(private LetterGeneratorService $letterService) {}

    public function index()
    {
        $templates  = LetterTemplate::all();
        $categories = [
            'Pemberitahuan Penting',
            'Undangan Acara',
            'Surat Pengantar',
            'Surat Keterangan',
            'Edaran RT',
        ];

        return view('admin.secretary.index', compact('templates', 'categories'));
    }

    /**
     * Generate the official RT letter PDF and return it as a download.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'subject'  => ['required', 'string', 'max:200'],
            'content'  => ['required', 'string', 'min:20'],
            'date'     => ['required', 'date'],
        ], [
            'content.min' => 'Isi surat minimal 20 karakter.',
        ]);

        return $this->letterService->download(
            $request->category,
            $request->subject,
            $request->content,
            $request->date,
        );
    }
}
