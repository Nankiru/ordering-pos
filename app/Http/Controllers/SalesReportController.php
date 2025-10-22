<?php

namespace App\Http\Controllers;

use App\Models\SalesReport;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    // Show monthly sales totals from sales_reports table
    public function index(Request $request)
    {
        $reports = SalesReport::orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $current = $reports->firstWhere(function ($r) {
            return (int) $r->year === (int) now()->year && (int) $r->month === (int) now()->month;
        });

        return view('sales_reports', [
            'reports' => $reports,
            'current' => $current,
        ]);
    }
}


