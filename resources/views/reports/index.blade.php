@extends('layouts.app')

@section('title', 'รายงานประวัติการขนย้ายมูลไก่')
@section('page_title', 'รายงานการขนย้ายและการค้นหา')

@section('styles')
<style>
    /* Filter Form Enhancements */
    .filter-card {
        border: none;
        border-radius: var(--border-radius-lg);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        background: #ffffff;
    }

    .filter-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.04);
        padding: 1.25rem 1.5rem;
    }

    .filter-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: #475569;
        margin-bottom: 0.35rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .filter-label i {
        color: var(--primary-accent);
        opacity: 0.85;
    }

    /* Custom Table container */
    .table-container-premium {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--border-radius-md);
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.01);
    }

    .premium-table-header {
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .premium-table-header th {
        color: #334155 !important;
        font-weight: 600 !important;
        font-size: 0.85rem !important;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem !important;
        text-transform: uppercase;
    }

    .table-hover tbody tr {
        transition: var(--transition-smooth);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(46, 125, 50, 0.015) !important;
    }

    /* License Plate Design */
    .license-plate-badge {
        background-color: #ffffff;
        color: #1e293b;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 4px 10px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        letter-spacing: 0.5px;
        min-width: 100px;
        text-align: center;
    }

    /* Premium Glassmorphic Status Badges */
    .status-badge-premium {
        border-radius: 30px;
        padding: 6px 12px;
        font-weight: 600;
        font-size: 0.775rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid;
    }

    .status-premium-pending {
        background-color: rgba(217, 119, 6, 0.06);
        color: #d97706;
        border-color: rgba(217, 119, 6, 0.15);
    }

    .status-premium-received {
        background-color: rgba(22, 163, 74, 0.06);
        color: #16a34a;
        border-color: rgba(22, 163, 74, 0.15);
    }

    .status-premium-cancelled {
        background-color: rgba(220, 38, 38, 0.06);
        color: #dc2626;
        border-color: rgba(220, 38, 38, 0.15);
    }

    /* Modern Photo Proof Badge Buttons */
    .photo-proof-badge {
        border-radius: 20px;
        padding: 5px 12px;
        font-weight: 500;
        font-size: 0.8rem;
        transition: var(--transition-smooth);
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border: 1px solid transparent;
        text-decoration: none;
    }

    .photo-proof-out {
        background-color: rgba(46, 125, 50, 0.06);
        color: #2e7d32;
        border-color: rgba(46, 125, 50, 0.12);
    }

    .photo-proof-out:hover {
        background-color: #2e7d32;
        color: #ffffff;
        transform: translateY(-1.5px);
        box-shadow: 0 4px 8px rgba(46, 125, 50, 0.15);
    }

    .photo-proof-in {
        background-color: rgba(37, 99, 235, 0.06);
        color: #2563eb;
        border-color: rgba(37, 99, 235, 0.12);
    }

    .photo-proof-in:hover {
        background-color: #2563eb;
        color: #ffffff;
        transform: translateY(-1.5px);
        box-shadow: 0 4px 8px rgba(37, 99, 235, 0.15);
    }

    /* Compact Side-by-Side Action Buttons */
    .action-btn-container {
        display: flex;
        gap: 6px;
        justify-content: center;
        align-items: center;
        min-width: 80px;
    }

    .action-btn-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid;
        background-color: #ffffff;
        font-size: 0.9rem;
        transition: var(--transition-smooth);
        padding: 0;
        cursor: pointer;
    }

    .action-btn-edit {
        color: #2e7d32;
        border-color: rgba(46, 125, 50, 0.25);
    }

    .action-btn-edit:hover {
        background-color: #2e7d32;
        border-color: #2e7d32;
        color: #ffffff;
        transform: scale(1.08);
        box-shadow: 0 3px 8px rgba(46, 125, 50, 0.2);
    }

    .action-btn-delete {
        color: #dc2626;
        border-color: rgba(220, 38, 38, 0.25);
    }

    .action-btn-delete:hover {
        background-color: #dc2626;
        border-color: #dc2626;
        color: #ffffff;
        transform: scale(1.08);
        box-shadow: 0 3px 8px rgba(220, 38, 38, 0.2);
    }

    .action-btn-container form {
        margin: 0;
        display: inline;
    }

    .reports-export-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .reports-table {
        min-width: 980px;
    }

    .reports-transfer-no {
        white-space: nowrap;
        letter-spacing: 0.2px;
    }

    @media (max-width: 767.98px) {
        .filter-card .card-header,
        .filter-card .card-body {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .reports-export-actions {
            width: 100%;
        }

        .reports-export-actions .btn {
            flex: 1 1 100%;
            justify-content: center;
        }

        .table-container-premium {
            margin: 0.75rem !important;
        }

        .premium-table-header th {
            white-space: nowrap;
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- ตัวกรองการค้นหา (Filters Form) -->
        <div class="card filter-card mb-4">
            <div class="card-header d-flex align-items-center">
                <span class="mb-0 fs-5 fw-semibold text-success"><i class="bi bi-funnel me-2"></i>ตัวกรองรายงานประวัติประมวลผล</span>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="{{ route('reports.index') }}" id="filter-form">
                    <div class="row g-3">
                        <!-- วันที่เริ่มต้น -->
                        <div class="col-xl-3 col-md-6 col-12">
                            <label for="start_date" class="filter-label"><i class="bi bi-calendar-event"></i>วันที่เริ่มต้น (ขาออก)</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                        </div>

                        <!-- วันที่สิ้นสุด -->
                        <div class="col-xl-3 col-md-6 col-12">
                            <label for="end_date" class="filter-label"><i class="bi bi-calendar-check"></i>วันที่สิ้นสุด (ขาออก)</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                        </div>

                        <!-- เลือกฟาร์ม -->
                        <div class="col-xl-3 col-md-6 col-12">
                            <label for="farm_id" class="filter-label"><i class="bi bi-house"></i>ฟาร์มต้นทาง</label>
                            <select class="form-select" id="farm_id" name="farm_id">
                                <option value="">-- ฟาร์มทั้งหมด --</option>
                                @foreach($farms as $farm)
                                    <option value="{{ $farm->id }}" {{ request('farm_id') == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- เลือกทะเบียนรถ -->
                        <div class="col-xl-3 col-md-6 col-12">
                            <label for="license_plate" class="filter-label"><i class="bi bi-card-text"></i>ทะเบียนรถ</label>
                            <input type="text" class="form-control" id="license_plate" name="license_plate" placeholder="พิมพ์ทะเบียนรถเพื่อค้นหา" value="{{ request('license_plate') }}">
                        </div>

                        <!-- เลือกกองมูลไก่ -->
                        <div class="col-xl-3 col-md-6 col-12">
                            <label for="pile_id" class="filter-label"><i class="bi bi-layers-half"></i>กองมูลไก่ปลายทาง</label>
                            <select class="form-select" id="pile_id" name="pile_id">
                                <option value="">-- กองทั้งหมด --</option>
                                @foreach($piles as $pile)
                                    <option value="{{ $pile->id }}" {{ request('pile_id') == $pile->id ? 'selected' : '' }}>{{ $pile->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- เลือกสถานะ -->
                        <div class="col-xl-3 col-md-6 col-12">
                            <label for="status" class="filter-label"><i class="bi bi-toggle-on"></i>สถานะรายการ</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">-- สถานะทั้งหมด --</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>รอรับเข้ากอง (Pending)</option>
                                <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>รับเข้ากองแล้ว (Received)</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ยกเลิก (Cancelled)</option>
                            </select>
                        </div>

                        <!-- ผู้บันทึกขาออก -->
                        <div class="col-xl-3 col-md-6 col-12">
                            <label for="out_user_id" class="filter-label"><i class="bi bi-person-badge"></i>ผู้บันทึกข้อมูลขาออก</label>
                            <select class="form-select" id="out_user_id" name="out_user_id">
                                <option value="">-- ผู้ใช้ทั้งหมด --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('out_user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- ปุ่มควบคุม -->
                        <div class="col-xl-3 col-12 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-2"><i class="bi bi-search"></i> ค้นหา</button>
                            <a href="{{ route('reports.index') }}" class="btn btn-light border py-2 w-100 text-center fw-medium d-inline-flex align-items-center justify-content-center">ล้างเงื่อนไข</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- รายการสรุปผลและแผง Export -->
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="fs-5 fw-semibold text-success d-inline-flex align-items-center gap-2">
                    <i class="bi bi-list-stars text-success"></i> ผลลัพธ์ข้อมูลประวัติการขนส่ง ({{ $transfers->total() }} เที่ยว)
                </span>
                
                <div class="reports-export-actions">
                    <!-- Export Excel CSV -->
                    <a href="{{ route('reports.excel', request()->all()) }}" class="btn btn-outline-success btn-sm rounded-pill px-3 py-1 fw-medium d-inline-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i> ส่งออก Excel (CSV)
                    </a>
                    
                    <!-- Print PDF -->
                    <a href="{{ route('reports.print', request()->all()) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fw-medium d-inline-flex align-items-center gap-2">
                        <i class="bi bi-printer-fill"></i> พิมพ์รายงาน (PDF)
                    </a>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <div class="table-container-premium m-3">
                        <table class="table table-hover align-middle mb-0 reports-table">
                            <thead class="premium-table-header">
                                <tr>
                                    <th class="px-4">เลขที่รายการ</th>
                                    <th>ฟาร์มต้นทาง</th>
                                    <th>ทะเบียนรถ</th>
                                    <th>น้ำหนัก (กก.)</th>
                                    <th>ขาออกจากฟาร์ม</th>
                                    <th>ขารับเข้ากอง</th>
                                    <th>สถานะ</th>
                                    <th>รูปหลักฐาน</th>
                                    @if(auth()->user()->isAdmin())
                                        <th class="text-center px-4" style="width: 120px;">จัดการ</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transfers as $transfer)
                                    <tr>
                                        <!-- เลขที่รายการ -->
                                        <td class="px-4 fw-semibold text-dark reports-transfer-no">{{ $transfer->transfer_no }}</td>

                                        <!-- ฟาร์มต้นทาง -->
                                        <td class="text-success fw-medium">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-house-door text-success opacity-75"></i>
                                                {{ $transfer->farm->name }}
                                            </div>
                                        </td>

                                        <!-- ทะเบียนรถ -->
                                        <td>
                                            <div class="license-plate-badge" title="ทะเบียนรถคันที่ใช้ขนย้าย">
                                                {{ $transfer->license_plate }}
                                            </div>
                                        </td>

                                        <!-- น้ำหนัก -->
                                        <td class="fw-bold text-dark">{{ number_format($transfer->weight, 2) }}</td>

                                        <!-- ขาออกจากฟาร์ม -->
                                        <td>
                                            <div class="fw-semibold text-secondary" style="font-size: 0.875rem;">
                                                <i class="bi bi-calendar3 me-1 text-muted"></i>{{ $transfer->out_datetime->format('d/m/Y') }}
                                                <span class="ms-1 fw-bold text-dark"><i class="bi bi-clock me-1 text-muted"></i>{{ $transfer->out_datetime->format('H:i') }} น.</span>
                                            </div>
                                            <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                                <i class="bi bi-person-fill me-1 text-muted opacity-75"></i>ออก: {{ $transfer->outUser->name }}
                                            </div>
                                        </td>

                                        <!-- ขารับเข้ากอง -->
                                        <td>
                                            @if($transfer->received_datetime)
                                                <div class="fw-semibold text-primary" style="font-size: 0.875rem;">
                                                    <i class="bi bi-layers-half me-1"></i>{{ $transfer->pile ? $transfer->pile->name : '-' }}
                                                </div>
                                                <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                                    <i class="bi bi-clock me-1 opacity-75"></i>{{ $transfer->received_datetime->format('d/m/Y H:i') }} น.
                                                </div>
                                                <div class="text-muted" style="font-size: 0.725rem;">
                                                    <i class="bi bi-person-check-fill me-1 opacity-75"></i>รับ: {{ $transfer->receiveUser ? $transfer->receiveUser->name : '-' }}
                                                </div>
                                            @else
                                                <span class="text-muted opacity-50">- ยังไม่ได้รับ -</span>
                                            @endif
                                        </td>

                                        <!-- สถานะ -->
                                        <td>
                                            @if($transfer->status === \App\Models\ManureTransfer::STATUS_PENDING)
                                                <span class="status-badge-premium status-premium-pending">
                                                    <i class="bi bi-hourglass-split"></i>รอรับเข้ากอง
                                                </span>
                                            @elseif($transfer->status === \App\Models\ManureTransfer::STATUS_RECEIVED)
                                                <span class="status-badge-premium status-premium-received">
                                                    <i class="bi bi-check-circle-fill"></i>รับเข้ากองแล้ว
                                                </span>
                                            @else
                                                <span class="status-badge-premium status-premium-cancelled">
                                                    <i class="bi bi-x-circle-fill"></i>ยกเลิก
                                                </span>
                                            @endif
                                        </td>

                                        <!-- รูปถ่ายหลักฐาน -->
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <!-- รูปขาออก -->
                                                <a href="#" class="photo-proof-badge photo-proof-out view-image" data-src="{{ $transfer->out_photo_url }}" data-title="หลักฐานรูปถ่ายขาออก: {{ $transfer->transfer_no }}">
                                                    <i class="bi bi-box-arrow-up-right"></i> ขาออก
                                                </a>

                                                <!-- รูปขารับ -->
                                                @if($transfer->receive_photo)
                                                    <a href="#" class="photo-proof-badge photo-proof-in view-image" data-src="{{ $transfer->receive_photo_url }}" data-title="หลักฐานรูปถ่ายขารับเข้ากอง: {{ $transfer->transfer_no }}">
                                                        <i class="bi bi-box-arrow-in-down-left"></i> ขารับ
                                                    </a>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- จัดการ (Admin) -->
                                        @if(auth()->user()->isAdmin())
                                            <td class="text-center px-4">
                                                <div class="action-btn-container">
                                                    <!-- ปุ่มแก้ไข -->
                                                    <a href="{{ route('reports.edit', $transfer) }}" class="action-btn-circle action-btn-edit" title="แก้ไขรายการขนส่ง" aria-label="แก้ไข">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>

                                                    <!-- ปุ่มลบ -->
                                                    <form method="POST" action="{{ route('reports.destroy', $transfer) }}" onsubmit="return confirm('คุณแน่ใจว่าต้องการลบรายการข้อมูลขนส่งเลขที่ {{ $transfer->transfer_no }} ใช่หรือไม่?\n⚠️ การลบจะไม่สามารถกู้คืนได้!');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="action-btn-circle action-btn-delete" title="ลบรายการขนส่ง" aria-label="ลบ">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->isAdmin() ? 9 : 8 }}" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                            ไม่พบประวัติข้อมูลขนย้ายตามเงื่อนไขที่กำหนด
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($transfers->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                <div class="px-3">
                    {{ $transfers->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal สำหรับพรีวิวรูปหลักฐาน -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--border-radius-lg); overflow: hidden; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.15);">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h5 class="modal-title fw-semibold d-inline-flex align-items-center gap-2" id="imagePreviewModalLabel">
                    <i class="bi bi-image text-success"></i> หลักฐานรูปถ่าย
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 bg-light d-flex align-items-center justify-content-center" style="min-height: 250px;">
                <img id="preview-modal-img" src="#" alt="Proof Document" class="img-fluid rounded shadow" style="max-height: 480px; object-fit: contain; width: 100%;">
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
                modalTitle.textContent = '';

                const titleIcon = document.createElement('i');
                titleIcon.className = 'bi bi-image text-success';
                modalTitle.appendChild(titleIcon);
                modalTitle.appendChild(document.createTextNode(' ' + this.getAttribute('data-title')));

                imageModal.show();
            });
        });
    });
</script>
@endsection
