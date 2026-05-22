<?php

namespace App\Http\Controllers;

use App\Models\ManurePile;
use App\Models\ManureTransfer;
use App\Support\TransferImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferInController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = ManureTransfer::with(['farm', 'outUser'])
            ->where('status', ManureTransfer::STATUS_PENDING);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('transfer_no', 'like', "%{$search}%")
                    ->orWhere('license_plate', 'like', "%{$search}%");
            });
        }

        $transfers = $query
            ->orderBy('out_datetime', 'desc')
            ->paginate(20)
            ->withQueryString();
        $piles = ManurePile::ordered()->get();
        $currentDateTime = Carbon::now('Asia/Bangkok')->format('Y-m-d\TH:i');

        return view('transfers.in', compact('transfers', 'piles', 'search', 'currentDateTime'));
    }

    public function receive(Request $request, $id)
    {
        $request->validate([
            'pile_id' => 'required|exists:manure_piles,id',
            'received_datetime' => 'required|date',
            'receive_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:15360',
        ], [
            'pile_id.required' => 'กรุณาเลือกกองมูลไก่ที่ต้องการลง',
            'pile_id.exists' => 'ไม่พบข้อมูลกองมูลไก่ที่เลือก',
            'received_datetime.required' => 'กรุณาระบุวันเวลารับเข้ากอง',
            'receive_photo.required' => 'กรุณาอัปโหลดรูปถ่ายยืนยันตอนรับเข้า',
            'receive_photo.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพเท่านั้น',
            'receive_photo.mimes' => 'รูปภาพต้องเป็นไฟล์สกุล jpg, jpeg, png หรือ webp เท่านั้น',
            'receive_photo.max' => 'ขนาดรูปถ่ายห้ามเกิน 15MB',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $transfer = ManureTransfer::lockForUpdate()->findOrFail($id);

                if ($transfer->status !== ManureTransfer::STATUS_PENDING) {
                    throw new \Exception('รายการนี้ได้รับการรับเข้าหรือถูกเปลี่ยนสถานะไปแล้ว ไม่สามารถรับซ้ำได้');
                }

                $photoPath = null;
                if ($request->hasFile('receive_photo')) {
                    $photoPath = TransferImage::optimizeAndStore(
                        $request->file('receive_photo'),
                        'in'
                    );
                }

                $transfer->update([
                    'pile_id' => $request->pile_id,
                    'received_datetime' => Carbon::parse($request->received_datetime)->format('Y-m-d H:i:s'),
                    'receive_photo' => $photoPath,
                    'receive_user_id' => auth()->id(),
                    'status' => ManureTransfer::STATUS_RECEIVED,
                ]);
            });

            return redirect()->route('transfers.in')
                ->with('success', 'ตรวจรับมูลไก่เข้ากองสำเร็จเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}
