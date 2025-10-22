<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

Artisan::command('sales:aggregate-month {--year=} {--month=}', function () {
	$year = (int) ($this->option('year') ?: now()->year);
	$month = (int) ($this->option('month') ?: now()->month);

	$total = \App\Models\Order::whereYear('created_at', $year)
		->whereMonth('created_at', $month)
		->selectRaw('COUNT(*) as total_orders, COALESCE(SUM(total),0) as total_amount')
		->first();

	$report = \App\Models\SalesReport::updateOrCreate(
		['year' => $year, 'month' => $month],
		['total_orders' => (int) ($total->total_orders ?? 0), 'total_amount' => (float) ($total->total_amount ?? 0)]
	);

	$this->info("Aggregated {$report->total_orders} orders, amount {$report->total_amount} for {$year}-{$month}");
})->purpose('Aggregate monthly sales totals into sales_reports table');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
