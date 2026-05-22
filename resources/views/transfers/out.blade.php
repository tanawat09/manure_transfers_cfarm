@extends('layouts.app')

@section('title', 'บันทึกขาออกจากฟาร์ม')
@section('page_title', 'บันทึกขาออกจากฟาร์ม')

@section('styles')
<style>
    .transfer-out-card {
        overflow: hidden;
    }

    .transfer-out-body .photo-preview-box {
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .transfer-out-body .camera-stage {
        aspect-ratio: 4 / 3;
        min-height: 200px;
    }

    .mobile-section-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .mobile-section-toggle .bi-chevron-down {
        transition: transform 0.2s ease;
    }

    .mobile-section-toggle[aria-expanded="true"] .bi-chevron-down {
        transform: rotate(180deg);
    }

    @media (max-width: 767.98px) {
        .transfer-out-body {
            padding: 1rem !important;
        }

        .transfer-out-body .row {
            --bs-gutter-y: 0.75rem;
        }

        .transfer-out-body .mb-3,
        .transfer-out-body .mb-4 {
            margin-bottom: 0.75rem !important;
        }

        .transfer-out-body .form-label {
            margin-bottom: 0.35rem;
            font-size: 0.95rem;
        }

        .transfer-out-body .form-control,
        .transfer-out-body .form-select,
        .transfer-out-body .input-group-text {
            font-size: 0.95rem;
            padding-top: 0.55rem;
            padding-bottom: 0.55rem;
        }

        .transfer-out-body .form-text,
        .transfer-out-body small {
            font-size: 0.76rem;
        }

        .transfer-out-body .camera-stage {
            min-height: 150px;
        }

        .transfer-out-body .photo-preview-box {
            min-height: 72px;
            padding: 0.5rem !important;
        }

        .transfer-out-body #photo-preview {
            max-height: 120px !important;
        }

        .mobile-action-bar {
            position: sticky;
            bottom: 0;
            z-index: 20;
            padding-top: 0.75rem;
            margin-top: 1rem;
            background: linear-gradient(to top, #ffffff 75%, rgba(255, 255, 255, 0));
        }

        .mobile-action-bar .btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-12">
        <div class="card transfer-out-card shadow-sm">
            <div class="card-header bg-success text-white">
                <span class="mb-0 fs-5 fw-medium"><i class="bi bi-box-arrow-up-right me-2"></i>ฟอร์มบันทึกข้อมูลมูลไก่ขาออก</span>
            </div>
            <div class="card-body p-4 transfer-out-body">
                <form action="{{ route('transfers.out.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-medium text-muted">เลขที่รายการ (อัตโนมัติ)</label>
                            <input type="text" class="form-control bg-light fw-semibold text-success border-success"
                                   value="{{ $suggestedNo }}" readonly title="เลขที่รายการจะถูกคำนวณและบันทึกอย่างปลอดภัย">
                            <small class="form-text text-muted">คำนวณจากรูปแบบ MF-ปีเดือนวัน-ลำดับ</small>
                        </div>

                        <div class="col-md-6 col-12 mt-3 mt-md-0">
                            <label for="out_datetime" class="form-label fw-medium">วันที่และเวลาออก <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control bg-light border-success fw-semibold"
                                   id="out_datetime" value="{{ $currentDateTime }}" readonly>
                            <small class="form-text text-muted">ระบบจะใช้วันและเวลาปัจจุบันอัตโนมัติ ไม่สามารถแก้ไขได้</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 col-12">
                            <label for="farm_id" class="form-label fw-medium">ฟาร์มต้นทาง <span class="text-danger">*</span></label>
                            <select class="form-select @error('farm_id') is-invalid @enderror" id="farm_id" name="farm_id" required>
                                <option value="" disabled selected>-- เลือกฟาร์มต้นทาง --</option>
                                @foreach($farms as $farm)
                                    <option value="{{ $farm->id }}" {{ old('farm_id') == $farm->id ? 'selected' : '' }}>
                                        {{ $farm->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('farm_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 col-12 mt-3 mt-md-0">
                            <label for="license_plate" class="form-label fw-medium">ทะเบียนรถ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('license_plate') is-invalid @enderror"
                                   id="license_plate" name="license_plate" value="{{ old('license_plate') }}"
                                   placeholder="ระบุป้ายทะเบียนรถ เช่น กข 1234 กรุงเทพฯ" required>
                            @error('license_plate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 col-12">
                            <label for="weight" class="form-label fw-medium">น้ำหนักมูลไก่ (กิโลกรัม) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control @error('weight') is-invalid @enderror"
                                       id="weight" name="weight" value="{{ old('weight') }}"
                                       placeholder="เช่น 12500..." required>
                                <span class="input-group-text bg-light text-muted">กก.</span>
                            </div>
                            @error('weight')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 col-12 mt-md-0">
                            <label class="form-label fw-medium">รูปถ่ายหลักฐานขาออก <span class="text-danger">*</span></label>

                            <div class="d-md-none mb-2">
                                <button class="btn btn-outline-success w-100 mobile-section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#photo-tools-collapse" aria-expanded="{{ $errors->has('out_photo') ? 'true' : 'false' }}" aria-controls="photo-tools-collapse">
                                    <span><i class="bi bi-camera me-2"></i>เปิดส่วนแนบรูปถ่าย</span>
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>

                            <div id="photo-tools-collapse" class="collapse d-md-block {{ $errors->has('out_photo') ? 'show' : '' }}">
                                <div class="btn-group w-100 mb-3" role="group" aria-label="Photo Input Mode">
                                    <input type="radio" class="btn-check" name="out_photo_mode" id="out_photo_mode_upload" value="upload" checked autocomplete="off">
                                    <label class="btn btn-outline-success border-success" for="out_photo_mode_upload">
                                        <i class="bi bi-folder2-open me-1"></i> อัปโหลดรูปภาพ
                                    </label>
                                    <input type="radio" class="btn-check" name="out_photo_mode" id="out_photo_mode_camera" value="camera" autocomplete="off">
                                    <label class="btn btn-outline-success border-success" for="out_photo_mode_camera">
                                        <i class="bi bi-camera me-1"></i> ถ่ายรูปภาพสดจากแอป
                                    </label>
                                </div>

                                <div id="upload-panel" class="mb-3">
                                    <input type="file" class="form-control @error('out_photo') is-invalid @enderror"
                                           id="out_photo" name="out_photo" accept="image/*" capture="environment" onchange="previewImage(event)" required>
                                    <small class="form-text text-muted">รองรับไฟล์ jpg, jpeg, png, webp ขนาดไม่เกิน 5MB</small>
                                    @error('out_photo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="camera-panel" class="d-none mb-3 p-3 border rounded bg-dark">
                                    <div class="position-relative overflow-hidden rounded bg-black d-flex align-items-center justify-content-center camera-stage">
                                        <video id="webcam-stream" class="w-100 h-100 d-none" style="object-fit: cover;" autoplay playsinline></video>
                                        <canvas id="webcam-canvas" class="d-none"></canvas>

                                        <div id="camera-placeholder" class="text-center text-white p-3">
                                            <i class="bi bi-camera fs-1 text-success d-block mb-2"></i>
                                            <span class="d-block fw-medium small">กล้องยังไม่ได้เปิดใช้งาน</span>
                                            <small class="text-muted" style="font-size: 0.75rem;">กดปุ่ม "เปิดใช้งานกล้อง" ด้านล่างเพื่อเริ่มถ่ายภาพ</small>
                                        </div>
                                    </div>

                                    <div class="mt-3 d-flex justify-content-center gap-2 flex-wrap">
                                        <button type="button" id="btn-camera-start" class="btn btn-success btn-sm">
                                            <i class="bi bi-camera-video-fill me-1"></i> เปิดใช้งานกล้อง
                                        </button>
                                        <button type="button" id="btn-camera-snap" class="btn btn-primary btn-sm d-none">
                                            <i class="bi bi-camera-fill me-1"></i> ถ่ายรูป
                                        </button>
                                        <button type="button" id="btn-camera-switch" class="btn btn-warning btn-sm d-none">
                                            <i class="bi bi-arrow-repeat me-1"></i> สลับกล้อง
                                        </button>
                                        <button type="button" id="btn-camera-stop" class="btn btn-danger btn-sm d-none">
                                            <i class="bi bi-camera-video-off-fill me-1"></i> ปิดกล้อง
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2 text-center bg-light p-2 rounded border photo-preview-box">
                                <img id="photo-preview" src="#" alt="Preview" class="img-fluid rounded d-none" style="max-height: 200px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                <span id="preview-placeholder" class="text-muted"><i class="bi bi-image fs-4 d-block"></i>ยังไม่ได้เลือกหรือถ่ายรูป</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-md-none mb-2">
                            <button class="btn btn-outline-secondary w-100 mobile-section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#remark-collapse" aria-expanded="{{ old('remark') ? 'true' : 'false' }}" aria-controls="remark-collapse">
                                <span><i class="bi bi-journal-text me-2"></i>เพิ่มหมายเหตุ</span>
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>

                        <div id="remark-collapse" class="collapse d-md-block {{ old('remark') ? 'show' : '' }}">
                            <label for="remark" class="form-label fw-medium">หมายเหตุ (ถ้ามี)</label>
                            <textarea class="form-control" id="remark" name="remark" rows="2" placeholder="กรอกข้อมูลเพิ่มเติม เช่น สภาพอากาศ, ปัญหารถเสีย...">{{ old('remark') }}</textarea>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mobile-action-bar">
                        <a href="{{ route('dashboard') }}" class="btn btn-light px-4 py-2 me-md-2">
                            ยกเลิก
                        </a>
                        <button type="submit" class="btn btn-primary px-5 py-2">
                            <i class="bi bi-check-circle me-1"></i> บันทึกขาออกฟาร์ม
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let cameraStream = null;
    let currentFacingMode = 'environment';

    document.querySelectorAll('input[name="out_photo_mode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const uploadPanel = document.getElementById('upload-panel');
            const cameraPanel = document.getElementById('camera-panel');
            const fileInput = document.getElementById('out_photo');

            if (this.value === 'camera') {
                uploadPanel.classList.add('d-none');
                cameraPanel.classList.remove('d-none');
                fileInput.removeAttribute('required');
            } else {
                uploadPanel.classList.remove('d-none');
                cameraPanel.classList.add('d-none');
                fileInput.setAttribute('required', 'required');
                stopWebcam();
            }
        });
    });

    function previewImage(event) {
        const reader = new FileReader();
        const preview = document.getElementById('photo-preview');
        const placeholder = document.getElementById('preview-placeholder');

        reader.onload = function() {
            preview.src = reader.result;
            preview.classList.remove('d-none');
            placeholder.classList.add('d-none');
        };

        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        } else {
            preview.src = '#';
            preview.classList.add('d-none');
            placeholder.classList.remove('d-none');
        }
    }

    async function startWebcam() {
        const video = document.getElementById('webcam-stream');
        const placeholder = document.getElementById('camera-placeholder');

        const btnStart = document.getElementById('btn-camera-start');
        const btnSnap = document.getElementById('btn-camera-snap');
        const btnSwitch = document.getElementById('btn-camera-switch');
        const btnStop = document.getElementById('btn-camera-stop');

        if (cameraStream) {
            stopWebcam();
        }

        try {
            placeholder.innerHTML = `<div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div><span class="d-block mt-2 small">กำลังเปิดกล้อง...</span>`;

            const constraints = {
                video: {
                    facingMode: { ideal: currentFacingMode },
                    width: { ideal: 1280 },
                    height: { ideal: 960 }
                },
                audio: false
            };

            cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = cameraStream;
            video.classList.remove('d-none');
            placeholder.classList.add('d-none');

            btnStart.classList.add('d-none');
            btnSnap.classList.remove('d-none');
            btnStop.classList.remove('d-none');

            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(device => device.kind === 'videoinput');
            if (videoDevices.length > 1) {
                btnSwitch.classList.remove('d-none');
            }
        } catch (err) {
            console.error('Camera access error:', err);
            placeholder.innerHTML = `
                <i class="bi bi-exclamation-triangle fs-1 text-danger d-block mb-2"></i>
                <span class="d-block fw-bold text-danger small">ไม่สามารถเปิดกล้องได้</span>
                <small class="text-muted" style="font-size: 0.7rem;">โปรดตรวจสอบสิทธิ์การใช้งานกล้องในเบราว์เซอร์</small>
            `;
            btnStart.classList.remove('d-none');
            btnSnap.classList.add('d-none');
            btnSwitch.classList.add('d-none');
            btnStop.classList.add('d-none');
        }
    }

    function switchWebcam() {
        currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
        startWebcam();
    }

    function stopWebcam() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }

        const video = document.getElementById('webcam-stream');
        const placeholder = document.getElementById('camera-placeholder');

        video.srcObject = null;
        video.classList.add('d-none');
        placeholder.classList.remove('d-none');
        placeholder.innerHTML = `
            <i class="bi bi-camera fs-1 text-success d-block mb-2"></i>
            <span class="d-block fw-medium small">กล้องปิดอยู่</span>
            <small class="text-muted" style="font-size: 0.75rem;">กดปุ่ม "เปิดใช้งานกล้อง" เพื่อเริ่มต้นถ่ายภาพ</small>
        `;

        document.getElementById('btn-camera-start').classList.remove('d-none');
        document.getElementById('btn-camera-snap').classList.add('d-none');
        document.getElementById('btn-camera-switch').classList.add('d-none');
        document.getElementById('btn-camera-stop').classList.add('d-none');
    }

    function snapPhoto() {
        const video = document.getElementById('webcam-stream');
        const canvas = document.getElementById('webcam-canvas');
        const preview = document.getElementById('photo-preview');
        const placeholder = document.getElementById('preview-placeholder');
        const fileInput = document.getElementById('out_photo');

        if (!cameraStream) {
            return;
        }

        const width = video.videoWidth || 640;
        const height = video.videoHeight || 480;

        canvas.width = width;
        canvas.height = height;

        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, width, height);

        canvas.toBlob(function(blob) {
            if (!blob) {
                return;
            }

            const file = new File([blob], 'camera_capture_' + new Date().getTime() + '.jpg', { type: 'image/jpeg' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            const reader = new FileReader();
            reader.onload = function() {
                preview.src = reader.result;
                preview.classList.remove('d-none');
                placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);

            stopWebcam();
        }, 'image/jpeg', 0.9);
    }

    document.getElementById('btn-camera-start').addEventListener('click', startWebcam);
    document.getElementById('btn-camera-snap').addEventListener('click', snapPhoto);
    document.getElementById('btn-camera-switch').addEventListener('click', switchWebcam);
    document.getElementById('btn-camera-stop').addEventListener('click', stopWebcam);

    window.addEventListener('beforeunload', stopWebcam);
</script>
@endsection
