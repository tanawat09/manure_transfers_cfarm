<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\ManurePile;
use App\Models\ManureTransfer;
use App\Models\User;
use App\Support\TransferImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
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

        return view('reports.index', compact(
            'farms',
            'piles',
            'users',
            'transfers'
        ));
    }

    public function edit(ManureTransfer $transfer)
    {
        $farms = Farm::orderBy('name')->get();
        $piles = ManurePile::ordered()->get();

        return view('reports.edit', compact('transfer', 'farms', 'piles'));
    }

    public function update(Request $request, ManureTransfer $transfer)
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'license_plate' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.01',
            'out_datetime' => 'required|date',
            'pile_id' => 'nullable|exists:manure_piles,id',
            'received_datetime' => 'nullable|date',
            'status' => 'required|in:' . implode(',', [
                ManureTransfer::STATUS_PENDING,
                ManureTransfer::STATUS_RECEIVED,
                ManureTransfer::STATUS_CANCELLED,
            ]),
            'remark' => 'nullable|string',
            'out_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:15360',
            'receive_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:15360',
        ], [
            'out_photo.max' => 'ขนาดรูปขาออกห้ามเกิน 15MB',
            'receive_photo.max' => 'ขนาดรูปขารับห้ามเกิน 15MB',
        ]);

        if ($validated['status'] === ManureTransfer::STATUS_PENDING) {
            $validated['pile_id'] = null;
            $validated['received_datetime'] = null;
            $validated['receive_user_id'] = null;
        }

        if ($validated['status'] === ManureTransfer::STATUS_CANCELLED) {
            $validated['pile_id'] = $validated['pile_id'] ?? null;
            $validated['received_datetime'] = $validated['received_datetime'] ?? null;
        }

        if ($request->hasFile('out_photo')) {
            if ($transfer->out_photo) {
                Storage::disk('public')->delete($transfer->out_photo);
            }

            $validated['out_photo'] = TransferImage::optimizeAndStore(
                $request->file('out_photo'),
                'out'
            );
        }

        if ($request->hasFile('receive_photo')) {
            if ($transfer->receive_photo) {
                Storage::disk('public')->delete($transfer->receive_photo);
            }

            $validated['receive_photo'] = TransferImage::optimizeAndStore(
                $request->file('receive_photo'),
                'in'
            );
        }

        $validated['out_datetime'] = Carbon::parse($validated['out_datetime'])->format('Y-m-d H:i:s');

        if (! empty($validated['received_datetime'])) {
            $validated['received_datetime'] = Carbon::parse($validated['received_datetime'])->format('Y-m-d H:i:s');
            $validated['receive_user_id'] = $transfer->receive_user_id ?: auth()->id();
        }

        $transfer->update($validated);

        return redirect()
            ->route('reports.index', $request->only([
                'start_date',
                'end_date',
                'farm_id',
                'license_plate',
                'pile_id',
                'status',
                'out_user_id',
                'page',
            ]))
            ->with('success', 'แก้ไขรายการเรียบร้อยแล้ว');
    }

    public function destroy(ManureTransfer $transfer)
    {
        if ($transfer->out_photo) {
            Storage::disk('public')->delete($transfer->out_photo);
        }

        if ($transfer->receive_photo) {
            Storage::disk('public')->delete($transfer->receive_photo);
        }

        $transfer->delete();

        return redirect()
            ->route('reports.index')
            ->with('success', 'ลบรายการเรียบร้อยแล้ว');
    }

    public function exportExcel(Request $request)
    {
        $query = ManureTransfer::with(['farm', 'pile', 'outUser', 'receiveUser']);
        $query = $this->applyFilters($request, $query);
        $transfers = $query->orderBy('out_datetime', 'desc')->get();

        $fileName = 'manure_transfers_' . Carbon::now()->format('Ymd_His') . '.csv';

        $response = new StreamedResponse(function () use ($transfers) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

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
                'หมายเหตุ',
            ]);

            foreach ($transfers as $transfer) {
                $statusTh = match ($transfer->status) {
                    ManureTransfer::STATUS_PENDING => 'รอรับเข้ากอง',
                    ManureTransfer::STATUS_RECEIVED => 'รับเข้ากองแล้ว',
                    default => 'ยกเลิก',
                };

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
                    $transfer->remark ?? '',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
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

    public function downloadPdf(Request $request)
    {
        $query = ManureTransfer::with(['farm', 'pile', 'outUser', 'receiveUser']);
        $query = $this->applyFilters($request, $query);
        $transfers = $query->orderBy('out_datetime', 'desc')->get();

        $tempDir = storage_path('framework/cache');
        foreach ([$tempDir, $tempDir . DIRECTORY_SEPARATOR . 'mpdf'] as $directory) {
            if (! is_dir($directory)) {
                mkdir($directory, 0775, true);
            }

            if (is_writable($directory) === false) {
                chmod($directory, 0775);
            }
        }

        $html = view('reports.pdf', [
            'transfers' => $transfers,
            'logoPath' => public_path('images/cfarm-logo.png'),
        ])->render();

        $pdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'garuda',
            'tempDir' => $tempDir,
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 8,
            'margin_bottom' => 8,
        ]);
        $pdf->autoScriptToLang = true;
        $pdf->autoLangToFont = true;
        $pdf->SetTitle('Manure Transfer Report');
        $pdf->WriteHTML($html);

        $filename = 'manure-transfer-report-' . now()->format('Ymd-His') . '.pdf';

        return response($pdf->Output($filename, Destination::STRING_RETURN), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
