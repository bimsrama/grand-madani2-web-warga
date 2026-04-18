<?php

namespace App\Http\Controllers;

use App\Models\BoardMember;
use App\Models\CommunityWidget;
use App\Models\FinancialReport;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $widgets = CommunityWidget::active()->get();
        $members = BoardMember::ordered()->get();

        // Current year RT 03 cash balance
        $year = now()->year;
        $income  = FinancialReport::where('year', $year)->sum('income');
        $expense = FinancialReport::where('year', $year)->sum('expense');
        $saldo   = $income - $expense;

        return view('public.home', compact('widgets', 'members', 'saldo', 'year'));
    }
}
