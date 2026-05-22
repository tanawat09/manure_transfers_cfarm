@extends('layouts.app')

@section('title', 'รายงานประวัติการขนย้ายมูลไก่')
@section('page_title', 'รายงานการขนย้ายและการค้นหา')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- ตัวกรองการค้นหา (Filters Form) -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <span class="mb-0 fs-5 fw-semibold text-success"><i class="bi bi-funnel me-2"></i>ตัวกรองรายงานประวัติ</span>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="{{ route('reports.index') }}" id="filter-form">
                    <div class="row g-3">
                        <!-- วันที่เริ่มต้น -->
                        <div class="col-xl-3 col-md-6 col-12">
                            <label for="start_date" class="form-label fw-medium text-muted">วันที่เริ่มต้น (ขาออก)</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                        </div>

                        <!-- วันที่สิ้นสุด -->
                        <div class="col-xl-3 col-md-6 col-12">
                            <label for="end_date" class="form-label fw-medium text-muted">วันที่สิ้นสุด (ขาออก)</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                        </div>

                        <!-- เลือกฟาร์ม -->
                        <div class="col-xl-3 col-md-6 col-12">
                            <label for="farm_id" class="form-label fw-medium text-muted">ฟาร์มต้นทาง</label>
                            <select class="form-select" id="farm_id" name="farm_id">
                                <option value="">-- ฟาร์มทั้งหมด --</option>
                                @foreach($farms as $farm)
                                    <option value="{{ $farm->id }}" {{ request('farm_id') == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- เลือกทะเบียนรถ -->
                        <div class="col-xl-3 col-md-6 col-12">
                            <label for="license_plate" class="form-label fw-medium text-muted">ทะเบียนรถ</label>
                            <input type="text" class="form-control" id="license_plate" name="license_plate" placeholder="พิมพ์ทะเบียนรถเพื่อค้นหา" value="{{ request('license_plate') }}">
                        </div>

                        <!-- เลือกกองมูลไก่ -->
                        <div class="col-xl-3 col-md-6 col-12">
                            <label for="pile_id" class="form-label fw-medium text-muted">กองมูลไก่ปลายทาง</label>
                            <select class="form-select" id="pile_id" name="pile_id">
                                <option value="">-- กองทั้งหมด --</option>
                                @foreach($piles as $pile)
                                    <option value="{{ $pile->id }}" {{ request('pile_id') == $pile->id ? 'selected' : '' }}>{{ $pile->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- เลือกสถานะ -->
                        <div class="col-xl-3 col-md-6 col-12">
                            <label for="status" class="form-label fw-medium text-muted">สถานะรายการ</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">-- สถานะทั้งหมด --</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>รอรับเข้ากอง (Pending)</option>
                                <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>รับเข้ากองแล้ว (Received)</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ยกเลิก (Cancelled)</option>
                            </select>
                        </div>

                        <!-- ผู้บันทึกขาออก -->
                        <div class="col-xl-3 col-md-6 col-12">
                            <label for="out_user_id" class="form-label fw-medium text-muted">ผู้บันทึกข้อมูลขาออก</label>
                            <select class="form-select" id="out_user_id" name="out_user_id">
                                <option value="">-- ผู้ใช้ทั้งหมด --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('out_user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- ปุ่มควบคุม -->
                        <div class="col-xl-3 col-12 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-search me-1"></i> ค้นหา</button>
                            <a href="{{ route('reports.index') }}" class="btn btn-light border py-2 w-100 text-center">ล้างเงื่อนไข</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- รายการสรุปผลและแผง Export -->
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="fs-5 fw-semibold text-success"><i class="bi bi-list-columns-reverse me-2"></i>ผลลัพธ์รายงานประวัติประมวลผล ({{ $transfers->total() }} เที่ยว)</span>
                
                <div class="d-flex gap-2">
                    <!-- Export Excel CSV -->
                    <a href="{{ route('reports.excel', request()->all()) }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> ส่งออก Excel (CSV)
                    </a>
                    
                    <!-- Print PDF -->
                    <a href="{{ route('reports.print', request()->all()) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="bi bi-printer me-1"></i> พิมพ์รายงาน (PDF)
                    </a>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">เลขที่รายการ</th>
                                <th>ฟาร์มต้นทาง</th>
                                <th>ทะเบียนรถ</th>
                                <th>น้ำหนัก (กก.)</th>
                                <th>ขาออกจากฟาร์ม</th>
                                <th>ขารับเข้ากอง</th>
                                <th>สถานะ</th>
                                <th>รูปถ่ายหลักฐาน</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers as $transfer)
                                <tr>
                                    <td class="px-4 fw-semibold text-dark">{{ $transfer->transfer_no }}</td>
                                    <td class="text-success fw-medium">{{ $transfer->farm->name }}</td>
                                    <td><span class="badge bg-light text-dark border px-2.5 py-1.5">{{ $transfer->license_plate }}</span></td>
                                    <td class="fw-semibold">{{ number_format($transfer->weight, 2) }}</td>
                                    <td>
                                        <div class="fw-semibold" style="font-size: 0.9rem;">{{ $transfer->out_datetime->format('d/m/Y H:i น.') }}</div>
                                        <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-person me-0.5"></i>ออก: {{ $transfer->outUser->name }}</small>
                                    </td>
                                    <td>
                                        @if($transfer->received_datetime)
                                            <div class="fw-semibold text-primary" style="font-size: 0.9rem;">
                                                <i class="bi bi-layers-half me-1"></i>{{ $transfer->pile ? $transfer->pile->name : '-' }}
                                            </div>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                {{ $transfer->received_datetime->format('d/m/Y H:i น.') }}
                                            </small>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                <i class="bi bi-person me-0.5"></i>รับ: {{ $transfer->receiveUser ? $transfer->receiveUser->name : '-' }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transfer->status === \App\Models\ManureTransfer::STATUS_PENDING)
                                            <span class="badge-pending"><i class="bi bi-hourglass-split me-1"></i>รอรับเข้ากอง</span>
                                        @elseif($transfer->status === \App\Models\ManureTransfer::STATUS_RECEIVED)
                                            <span class="badge-received"><i class="bi bi-check-circle me-1"></i>รับเข้ากองแล้ว</span>
                                        @else
                                            <span class="badge-cancelled"><i class="bi bi-x-circle me-1"></i>ยกเลิก</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1.5">
                                            <!-- Outward Photo Thumb -->
                                            <a href="#" class="view-image" data-src="{{ $transfer->out_photo_url }}" data-title="หลักฐานขาออก: {{ $transfer->transfer_no }}">
                                                <span class="badge bg-success-subtle text-success border border-success-subtle" title="ดูรูปขาออก"><i class="bi bi-box-arrow-up-right me-0.5"></i>ขาออก</span>
                                            </a>

                                            <!-- Inward Photo Thumb -->
                                            @if($transfer->receive_photo)
                                                <a href="#" class="view-image" data-src="{{ $transfer->receive_photo_url }}" data-title="หลักฐานรับเข้ากอง: {{ $transfer->transfer_no }}">
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" title="ดูรูปรับเข้า"><i class="bi bi-box-arrow-in-down-left me-0.5"></i>ขารับ</span>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        ไม่พบประวัติข้อมูลขนย้ายตามเงื่อนไขที่กำหนด
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($transfers->hasPages())
            <div class="card-footer bg-white">
                {{ $transfers->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal สำหรับพรีวิวรูปหลักฐาน -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--border-radius-lg); overflow: hidden; border: none;">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="imagePreviewModalLabel">หลักฐานรูปถ่าย</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 bg-light">
                <img id="preview-modal-img" src="#" alt="Proof Document" class="img-fluid rounded shadow-sm" style="max-height: 450px;">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
        const modalImg = document.getElementById('preview-modal-img');
        const modalTitle = document.getElementById('imagePreviewModalLabel');

        document.querySelectorAll('.view-image').forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                modalImg.src = this.getAttribute('data-src');
                modalTitle.textContent = this.getAttribute('data-title');
                imageModal.show();
            });
        });
    });
</script>
@endsection
