@extends('layouts.app')

@section('title', 'จัดการผู้ใช้')
@section('page_title', 'ข้อมูลผู้ใช้และสิทธิ์')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="mb-0 fs-5"><i class="bi bi-people me-2"></i>รายชื่อบัญชีผู้ใช้ในระบบ</span>
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> เพิ่มบัญชีผู้ใช้ใหม่
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4" style="width: 80px;">ลำดับ</th>
                                <th>ชื่อผู้ใช้</th>
                                <th>อีเมล (Email)</th>
                                <th>สิทธิ์การเข้าใช้งาน (Role)</th>
                                <th style="width: 180px;" class="text-end px-4">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $user)
                                <tr>
                                    <td class="px-4 fw-semibold text-muted">{{ ($users->firstItem() ?? 1) + $index }}</td>
                                    <td>
                                        <div class="fw-semibold text-success fs-6">{{ $user->name }}</div>
                                    </td>
                                    <td>
                                        <div class="text-muted">{{ $user->email }}</div>
                                    </td>
                                    <td>
                                        @if($user->isAdmin())
                                            <span class="badge bg-danger rounded-pill px-2.5 py-1.5 fs-7">ผู้ดูแลระบบ (Admin)</span>
                                        @elseif($user->isStaff())
                                            <span class="badge bg-success rounded-pill px-2.5 py-1.5 fs-7">เจ้าหน้าที่ปฏิบัติงาน (Staff)</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill px-2.5 py-1.5 fs-7">ผู้ดูรายงาน / Viewer</span>
                                        @endif
                                    </td>
                                    <td class="text-end px-4">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-primary btn-sm me-1">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันที่จะลบผู้ใช้งานคนนี้ใช่หรือไม่?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-outline-secondary btn-sm" disabled title="ไม่สามารถลบตัวเองได้">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if($users->hasPages())
            <div class="card-footer bg-white">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
