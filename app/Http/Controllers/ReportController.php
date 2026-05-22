<?php

namespace App\Http\Controllers;

use App\Models\ManureTransfer;
use App\Models\Farm;
use App\Models\ManurePile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private function applyFilters(Request $request, $query)
    {
        if ($request->filled('start_date')) {
            $query->whereDate('out_datetime', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('out_datetime', '<=', $request->end_date);
        }
        if ($request->filled('farm_id')) {
            $query->where('farm_id', $request->farm_id);
        }
        if ($request->filled('license_plate')) {
            $query->where('license_plate', 'like', '%' . $request->license_plate . '%');
        }
        if ($request->filled('pile_id')) {
            $query->where('pile_id', $request->pile_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('out_user_id')) {
            $query->where('out_user_id', $request->out_user_id);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $farms = Farm::orderBy('name')->get();
        $piles = ManurePile::ordered()->get();
        $users = User::orderBy('name')->get();

        $query = ManureTransfer::with(['farm', 'pile', 'outUser', 'receiveUser']);
        $query = $this->applyFilters($request, $query);

        $transfers = $query
            ->orderBy('out_datetime', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('reports.index', compact('farms', 'piles', 'users', 'transfers'));
    }

    public function exportExcel(Request $request)
    {
        $query = ManureTransfer::with(['farm', 'pile', 'outUser', 'receiveUser']);
        $query = $this->applyFilters($request, $query);
        $transfers = $query->orderBy('out_datetime', 'desc')->get();

        $fileName = 'manure_transfers_' . Carbon::now()->format('Ymd_His') . '.csv';

        $response = new StreamedResponse(function () use ($transfers) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM to fix Excel Thai character display issues
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Column Headers
            fputcsv($handle, [
                'เลขที่รายการ',
                'ฟาร์มต้นทาง',
                'ทะเบียนรถ',
                'น้ำหนัก (กก.)',
                'วันเวลาออกจากฟาร์ม',
                'วันเวลารับเข้ากอง',
                'กองที่ลง',
                'สถานะ',
                'ผู้บันทึกขาออก',
                'ผู้รับเข้ากอง',
                'หมายเหตุ'
            ]);

            // Data rows
            foreach ($transfers as $transfer) {
                $statusTh = '';
                if ($transfer->status === ManureTransfer::STATUS_PENDING) {
                    $statusTh = 'รอรับเข้ากอง';
                } elseif ($transfer->status === ManureTransfer::STATUS_RECEIVED) {
                    $statusTh = 'รับเข้ากองแล้ว';
                } else {
                    $statusTh = 'ยกเลิก';
                }

                fputcsv($handle, [
                    $transfer->transfer_no,
                    $transfer->farm->name,
                    $transfer->license_plate,
                    $transfer->weight,
                    $transfer->out_datetime->format('d/m/Y H:i'),
                    $transfer->received_datetime ? $transfer->received_datetime->format('d/m/Y H:i') : '-',
                    $transfer->pile ? $transfer->pile->name : '-',
                    $statusTh,
                    $transfer->outUser->name,
                    $transfer->receiveUser ? $transfer->receiveUser->name : '-',
                    $transfer->remark ?? ''
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);

        return $response;
    }

    public function print(Request $request)
    {
        $query = ManureTransfer::with(['farm', 'pile', 'outUser', 'receiveUser']);
        $query = $this->applyFilters($request, $query);
        $transfers = $query->orderBy('out_datetime', 'desc')->get();

        return view('reports.print', compact('transfers'));
    }
}
