<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceItem;
use Illuminate\Http\Request;

class PublicMarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $preloved = MarketplaceItem::withoutGlobalScopes()
            ->where('rt_number', '3')
            ->where('category', 'preloved')
            ->where('is_active', true)
            ->latest()
            ->get();

        $jasa = MarketplaceItem::withoutGlobalScopes()
            ->where('rt_number', '3')
            ->where('category', 'jasa')
            ->where('is_active', true)
            ->latest()
            ->get();

        return view('public.marketplace', compact('preloved', 'jasa'));
    }
}
