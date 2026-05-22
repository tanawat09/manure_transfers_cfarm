@extends('layouts.app')

@section('title', 'บันทึกสำเร็จ')
@section('page_title', 'ทำรายการสำเร็จ')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9 col-12">
        <div class="card border-0 shadow-lg" style="overflow: hidden;">
            <!-- Top Banner -->
            <div class="bg-success text-white text-center py-5">
                <i class="bi bi-check-circle-fill display-2 mb-3 animate__animated animate__zoomIn"></i>
                <h3 class="fw-semibold">บันทึกข้อมูลขาออกสำเร็จ!</h3>
                <p class="mb-0 opacity-75">เลขที่รายการได้รับการจัดสรรจากระบบอย่างสมบูรณ์</p>
            </div>

            <!-- Receipt Container -->
            <div class="card-body p-4 p-md-5 bg-white">
                <div class="text-center mb-4">
                    <span class="fs-4 fw-bold text-success border border-success border-2 px-3 py-1.5 rounded" style="letter-spacing: 1px;">
                        {{ $transfer->transfer_no }}
                    </span>
                </div>

                <div class="row g-3 border-bottom pb-4 mb-4">
                    <div class="col-sm-6 col-12">
                        <small class="text-muted d-block">ฟาร์มต้นทาง</small>
                        <span class="fw-semibold fs-6 text-dark">{{ $transfer->farm->name }}</span>
                    </div>
                    <div class="col-sm-6 col-12">
                        <small class="text-muted d-block">ทะเบียนรถ</small>
                        <span class="fw-semibold fs-6 text-dark">{{ $transfer->license_plate }}</span>
                    </div>
                    <div class="col-sm-6 col-12">
                        <small class="text-muted d-block">น้ำหนักมูลไก่</small>
                        <span class="fw-semibold fs-5 text-success">{{ number_format($transfer->weight, 2) }} <span class="fs-7 text-muted">กิโลกรัม</span></span>
                    </div>
                    <div class="col-sm-6 col-12">
                        <small class="text-muted d-block">วันเวลาที่ออกจากฟาร์ม</small>
                        <span class="fw-semibold fs-6 text-dark">{{ $transfer->out_datetime->format('d/m/Y H:i น.') }}</span>
                    </div>
                    <div class="col-sm-6 col-12">
                        <small class="text-muted d-block">ผู้บันทึกข้อมูล</small>
                        <span class="fw-semibold fs-6 text-dark"><i class="bi bi-person me-1"></i>{{ $transfer->outUser->name }}</span>
                    </div>
                    <div class="col-sm-6 col-12">
                        <small class="text-muted d-block">สถานะรายการ</small>
                        <span class="badge badge-pending"><i class="bi bi-clock me-1"></i>รอรับเข้ากองปลายทาง</span>
                    </div>
                </div>

                <!-- Proof image display -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-muted mb-2">รูปถ่ายหลักฐานขาออก</label>
                    <div class="text-center p-2 border bg-light rounded">
                        <img src="{{ asset('storage/' . $transfer->out_photo) }}" alt="Outward photo" class="img-fluid rounded shadow-sm" style="max-height: 250px;">
                    </div>
                </div>

                @if($transfer->remark)
                <div class="mb-4 p-3 bg-light rounded border-start border-success border-3">
                    <small class="text-muted d-block fw-semibold">หมายเหตุ</small>
                    <p class="mb-0 text-dark" style="font-size: 0.9rem;">{{ $transfer->remark }}</p>
                </div>
                @endif

                <!-- Navigation buttons -->
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center pt-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-success px-4 py-2 me-sm-2">
                        <i class="bi bi-speedometer2 me-1"></i> ไปที่ Dashboard
                    </a>
                    <a href="{{ route('transfers.out') }}" class="btn btn-primary px-4 py-2">
                        <i class="bi bi-plus-lg me-1"></i> บันทึกขาออกเพิ่ม
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
