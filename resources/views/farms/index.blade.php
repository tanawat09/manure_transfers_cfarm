@extends('layouts.app')

@section('title', 'จัดการฟาร์ม')
@section('page_title', 'ข้อมูลฟาร์ม')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="mb-0 fs-5"><i class="bi bi-house-gear me-2"></i>รายชื่อฟาร์มทั้งหมด</span>
                <a href="{{ route('farms.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> เพิ่มฟาร์มใหม่
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4" style="width: 80px;">ลำดับ</th>
                                <th>ชื่อฟาร์ม</th>
                                <th>จำนวนเที่ยวขนย้ายสะสม</th>
                                <th style="width: 180px;" class="text-end px-4">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($farms as $index => $farm)
                                <tr>
                                    <td class="px-4 fw-semibold text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-success fs-6">{{ $farm->name }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary px-2.5 py-1.5 rounded-pill fs-7">
                                            {{ $farm->transfers_count }} เที่ยว
                                        </span>
                                    </td>
                                    <td class="text-end px-4">
                                        <a href="{{ route('farms.edit', $farm->id) }}" class="btn btn-outline-primary btn-sm me-1">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('farms.destroy', $farm->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันที่จะลบข้อมูลฟาร์มนี้ใช่หรือไม่?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        ยังไม่มีข้อมูลฟาร์มในระบบ
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
