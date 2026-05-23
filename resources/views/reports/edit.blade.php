@extends('layouts.app')

@section('title', 'แก้ไขรายการรายงาน')
@section('page_title', 'แก้ไขรายการรายงาน')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="fw-semibold text-success fs-5">
                    <i class="bi bi-pencil-square me-2"></i>แก้ไขรายการ {{ $transfer->transfer_no }}
                </div>
                <a href="{{ route('reports.index') }}" class="btn btn-light border">กลับไปรายงาน</a>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('reports.update', $transfer) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ฟาร์มต้นทาง</label>
                            <select name="farm_id" class="form-select @error('farm_id') is-invalid @enderror" required>
                                @foreach($farms as $farm)
                                    <option value="{{ $farm->id }}" @selected(old('farm_id', $transfer->farm_id) == $farm->id)>{{ $farm->name }}</option>
                                @endforeach
                            </select>
                            @error('farm_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">ทะเบียนรถ</label>
                            <input type="text" name="license_plate" class="form-control @error('license_plate') is-invalid @enderror" value="{{ old('license_plate', $transfer->license_plate) }}" required>
                            @error('license_plate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">น้ำหนัก (กก.)</label>
                            <input type="number" step="0.01" name="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight', $transfer->weight) }}" required>
                            @error('weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">วันเวลาออกจากฟาร์ม</label>
                            <input type="datetime-local" name="out_datetime" class="form-control @error('out_datetime') is-invalid @enderror" value="{{ old('out_datetime', optional($transfer->out_datetime)->format('Y-m-d\TH:i')) }}" required>
                            @error('out_datetime')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">สถานะ</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="pending" @selected(old('status', $transfer->status) === 'pending')>รอรับเข้ากอง</option>
                                <option value="received" @selected(old('status', $transfer->status) === 'received')>รับเข้ากองแล้ว</option>
                                <option value="cancelled" @selected(old('status', $transfer->status) === 'cancelled')>ยกเลิก</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">กองปลายทาง</label>
                            <select name="pile_id" class="form-select @error('pile_id') is-invalid @enderror">
                                <option value="">-- ไม่ระบุ --</option>
                                @foreach($piles as $pile)
                                    <option value="{{ $pile->id }}" @selected(old('pile_id', $transfer->pile_id) == $pile->id)>{{ $pile->name }}</option>
                                @endforeach
                            </select>
                            @error('pile_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">วันเวลารับเข้ากอง</label>
                            <input type="datetime-local" name="received_datetime" class="form-control @error('received_datetime') is-invalid @enderror" value="{{ old('received_datetime', optional($transfer->received_datetime)->format('Y-m-d\TH:i')) }}">
                            @error('received_datetime')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="remark" rows="3" class="form-control @error('remark') is-invalid @enderror">{{ old('remark', $transfer->remark) }}</textarea>
                            @error('remark')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">รูปขาออกใหม่</label>
                            <input type="file" name="out_photo" class="form-control @error('out_photo') is-invalid @enderror" accept="image/*">
                            @error('out_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if($transfer->out_photo_url)
                                <div class="mt-2">
                                    <img src="{{ $transfer->out_photo_url }}" alt="Out photo" class="img-fluid rounded border" style="max-height: 180px;">
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">รูปขารับใหม่</label>
                            <input type="file" name="receive_photo" class="form-control @error('receive_photo') is-invalid @enderror" accept="image/*">
                            @error('receive_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if($transfer->receive_photo_url)
                                <div class="mt-2">
                                    <img src="{{ $transfer->receive_photo_url }}" alt="Receive photo" class="img-fluid rounded border" style="max-height: 180px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2 justify-content-end">
                        <a href="{{ route('reports.index') }}" class="btn btn-light border">ยกเลิก</a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
