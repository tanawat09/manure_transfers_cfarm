<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\ManureTransfer;
use App\Support\TransferImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferOutController extends Controller
{
    public function create()
    {
        $farms = Farm::orderBy('name')->get();

        $dateStr = Carbon::now('Asia/Bangkok')->format('Ymd');
        $prefix = 'MF-' . $dateStr . '-';
        $lastTransfer = ManureTransfer::where('transfer_no', 'like', $prefix . '%')
            ->orderBy('transfer_no', 'desc')
            ->first();

        $newNum = '001';
        if ($lastTransfer) {
            $lastNum = (int) substr($lastTransfer->transfer_no, -3);
            $newNum = str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
        }

        $suggestedNo = $prefix . $newNum;
        $currentDateTime = Carbon::now('Asia/Bangkok')->format('Y-m-d\TH:i');

        return view('transfers.out', compact('farms', 'suggestedNo', 'currentDateTime'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'license_plate' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.01',
            'out_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:15360',
            'remark' => 'nullable|string',
        ], [
            'farm_id.required' => 'กรุณาเลือกฟาร์มต้นทาง',
            'farm_id.exists' => 'ไม่พบข้อมูลฟาร์มที่เลือก',
            'license_plate.required' => 'กรุณากรอกทะเบียนรถ',
            'license_plate.string' => 'ทะเบียนรถต้องเป็นข้อความเท่านั้น',
            'license_plate.max' => 'ทะเบียนรถห้ามเกิน 255 ตัวอักษร',
            'weight.required' => 'กรุณากรอกน้ำหนักมูลไก่',
            'weight.numeric' => 'น้ำหนักต้องเป็นตัวเลขเท่านั้น',
            'weight.min' => 'น้ำหนักต้องมากกว่า 0',
            'out_photo.required' => 'กรุณาอัปโหลดรูปถ่ายขาออก',
            'out_photo.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพเท่านั้น',
            'out_photo.mimes' => 'รูปภาพต้องเป็นไฟล์สกุล jpg, jpeg, png หรือ webp เท่านั้น',
            'out_photo.max' => 'ขนาดรูปถ่ายห้ามเกิน 15MB',
        ]);

        try {
            $transfer = DB::transaction(function () use ($request) {
                $dateStr = Carbon::now('Asia/Bangkok')->format('Ymd');
                $prefix = 'MF-' . $dateStr . '-';

                $lastTransfer = ManureTransfer::where('transfer_no', 'like', $prefix . '%')
                    ->lockForUpdate()
                    ->orderBy('transfer_no', 'desc')
                    ->first();

                $newNum = '001';
                if ($lastTransfer) {
                    $lastNum = (int) substr($lastTransfer->transfer_no, -3);
                    $newNum = str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
                }

                $transferNo = $prefix . $newNum;

                $photoPath = null;
                if ($request->hasFile('out_photo')) {
                    $photoPath = TransferImage::optimizeAndStore(
                        $request->file('out_photo'),
                        'out'
                    );
                }

                return ManureTransfer::create([
                    'transfer_no' => $transferNo,
                    'farm_id' => $request->farm_id,
                    'license_plate' => $request->license_plate,
                    'weight' => $request->weight,
                    'out_datetime' => Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s'),
                    'out_photo' => $photoPath,
                    'out_user_id' => auth()->id(),
                    'status' => ManureTransfer::STATUS_PENDING,
                    'remark' => $request->remark,
                ]);
            });

            return redirect()->route('transfers.out_success', $transfer->id)
                ->with('success', 'บันทึกข้อมูลขาออกจากฟาร์มเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage());
        }
    }

    public function success($id)
    {
        $transfer = ManureTransfer::with(['farm', 'outUser'])->findOrFail($id);

        return view('transfers.out-success', compact('transfer'));
    }
}
