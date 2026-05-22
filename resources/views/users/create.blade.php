@extends('layouts.app')

@section('title', 'เพิ่มบัญชีผู้ใช้ใหม่')
@section('page_title', 'เพิ่มบัญชีผู้ใช้ใหม่')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-12">
        <div class="card">
            <div class="card-header">
                <span class="mb-0 fs-5"><i class="bi bi-person-plus me-2"></i>กรอกข้อมูลบัญชีผู้ใช้ใหม่</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">ชื่อผู้ใช้งาน <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" 
                               placeholder="เช่น สมชาย ใจดี..." required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">ที่อยู่อีเมล (Email) <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" 
                               placeholder="เช่น somchai@example.com..." required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium">รหัสผ่านสำหรับเข้าใช้งาน <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" 
                               placeholder="ความยาวอย่างน้อย 8 ตัวอักษร..." required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label fw-medium">สิทธิ์การเข้าใช้งาน <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="" disabled selected>-- เลือกสิทธิ์การเข้าใช้งาน --</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>ผู้ดูแลระบบ (Admin)</option>
                            <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>เจ้าหน้าที่ปฏิบัติงาน (Staff)</option>
                            <option value="viewer" {{ old('role') === 'viewer' ? 'selected' : '' }}>ผู้ดูรายงาน (Viewer)</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between pt-2">
                        <a href="{{ route('users.index') }}" class="btn btn-light px-4">
                            <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
