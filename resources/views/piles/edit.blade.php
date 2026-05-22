@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลกองมูลไก่')
@section('page_title', 'แก้ไขข้อมูลกองมูลไก่')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-12">
        <div class="card">
            <div class="card-header">
                <span class="mb-0 fs-5"><i class="bi bi-pencil-square me-2"></i>แก้ไขข้อมูลกองมูลไก่</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('piles.update', $pile->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="name" class="form-label fw-medium">ชื่อกองมูลไก่ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $pile->name) }}" 
                               placeholder="เช่น กอง 1..." required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between pt-2">
                        <a href="{{ route('piles.index') }}" class="btn btn-light px-4">
                            <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
