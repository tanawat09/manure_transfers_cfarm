<?php

namespace App\Http\Controllers;

use App\Models\ManureTransfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $allowedPeriods = [1, 7, 30, 60, 90];
        $selectedPeriod = (int) $request->integer('days', 7);

        if (! in_array($selectedPeriod, $allowedPeriods, true)) {
            $selectedPeriod = 7;
        }

        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $todayEnd = Carbon::now()->endOfDay();
        $periodStart = Carbon::now()->subDays($selectedPeriod - 1)->startOfDay();

        $fromDate = $request->filled('from_date')
            ? Carbon::parse($request->input('from_date'))->startOfDay()
            : null;

        $toDate = $request->filled('to_date')
            ? Carbon::parse($request->input('to_date'))->endOfDay()
            : null;

        if ($fromDate && ! $toDate) {
            $toDate = $todayEnd->copy();
        }

        if (! $fromDate && $toDate) {
            $fromDate = $toDate->copy()->startOfDay();
        }

        $isCustomRange = $fromDate !== null || $toDate !== null;

        if ($isCustomRange) {
            $periodStart = $fromDate ?? $periodStart;
            $todayEnd = $toDate ?? $todayEnd;
            $selectedPeriod = (int) $periodStart->diffInDays($todayEnd) + 1;
        }

        $outboundTrips = ManureTransfer::whereBetween('out_datetime', [$periodStart, $todayEnd])->count();

        $receivedTrips = ManureTransfer::where('status', ManureTransfer::STATUS_RECEIVED)
            ->whereBetween('received_datetime', [$periodStart, $todayEnd])
            ->count();

        $pendingTransfers = ManureTransfer::where('status', ManureTransfer::STATUS_PENDING)->count();

        $totalWeight = ManureTransfer::whereBetween('out_datetime', [$periodStart, $todayEnd])->sum('weight');

        $farmWeights = ManureTransfer::select('farms.name as farm_name', DB::raw('SUM(weight) as total_weight'))
            ->join('farms', 'manure_transfers.farm_id', '=', 'farms.id')
            ->whereBetween('out_datetime', [$periodStart, $todayEnd])
            ->groupBy('farms.id', 'farms.name')
            ->orderByDesc('total_weight')
            ->get();

        $pileWeights = ManureTransfer::select('manure_piles.name as pile_name', DB::raw('SUM(weight) as total_weight'))
            ->join('manure_piles', 'manure_transfers.pile_id', '=', 'manure_piles.id')
            ->where('status', ManureTransfer::STATUS_RECEIVED)
            ->whereBetween('received_datetime', [$periodStart, $todayEnd])
            ->groupBy('manure_piles.id', 'manure_piles.name')
            ->orderByDesc('total_weight')
            ->get();

        $dailyTransfers = ManureTransfer::selectRaw('DATE(out_datetime) as summary_date')
            ->selectRaw('COUNT(*) as trip_count')
            ->selectRaw('COALESCE(SUM(weight), 0) as total_weight')
            ->whereBetween('out_datetime', [$periodStart, $todayEnd])
            ->groupBy('summary_date')
            ->orderBy('summary_date')
            ->get()
            ->keyBy('summary_date');

        $dailyReceived = ManureTransfer::selectRaw('DATE(received_datetime) as summary_date')
            ->selectRaw('COUNT(*) as trip_count')
            ->where('status', ManureTransfer::STATUS_RECEIVED)
            ->whereBetween('received_datetime', [$periodStart, $todayEnd])
            ->groupBy('summary_date')
            ->orderBy('summary_date')
            ->get()
            ->keyBy('summary_date');

        $dateLabels = [];
        $tripSeries = [];
        $weightSeries = [];
        $receivedSeries = [];

        $cursor = $periodStart->copy();
        while ($cursor <= $todayEnd) {
            $dateKey = $cursor->toDateString();
            $dateLabels[] = $cursor->locale('th')->translatedFormat('d M');
            $tripSeries[] = (int) ($dailyTransfers[$dateKey]->trip_count ?? 0);
            $weightSeries[] = (float) ($dailyTransfers[$dateKey]->total_weight ?? 0);
            $receivedSeries[] = (int) ($dailyReceived[$dateKey]->trip_count ?? 0);
            $cursor->addDay();
        }

        $latestTransfers = ManureTransfer::with(['farm', 'pile', 'outUser', 'receiveUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'allowedPeriods',
            'selectedPeriod',
            'periodStart',
            'todayEnd',
            'fromDate',
            'toDate',
            'isCustomRange',
            'outboundTrips',
            'receivedTrips',
            'pendingTransfers',
            'totalWeight',
            'farmWeights',
            'pileWeights',
            'dateLabels',
            'tripSeries',
            'weightSeries',
            'receivedSeries',
            'latestTransfers'
        ));
    }
}
