@extends('layouts.app')

@section('title', 'ตรวจรับเข้ากองปลายทาง')
@section('page_title', 'ตรวจรับมูลไก่เข้ากอง')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- สรุปจำนวนรายการรอตรวจรับ -->
        <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm border-0" role="alert" style="border-radius: 12px;">
            <i class="bi bi-truck fs-3 me-3"></i>
            <div>
                <strong class="fs-5">{{ $transfers->total() }} รายการ</strong>
                <span class="d-block d-md-inline ms-md-2 text-success">รถขนมูลไก่ที่ออกจากฟาร์มและรอตรวจรับเข้ากอง</span>
            </div>
        </div>

        <!-- ค้นหารายการ (ย่อลงเป็นแถบเล็ก) -->
        @if($transfers->total() > 3)
        <div class="mb-3">
            <form method="GET" action="{{ route('transfers.in') }}" class="input-group input-group-sm" style="max-width: 400px;">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" name="search" value="{{ $search }}" placeholder="กรองด้วย เลขที่ หรือ ทะเบียนรถ...">
                <button type="submit" class="btn btn-outline-success btn-sm"><i class="bi bi-funnel"></i> กรอง</button>
                @if($search)
                    <a href="{{ route('transfers.in') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>
        @endif

        <!-- แสดงรายการรถขนมูลไก่ที่รอตรวจรับ (Card-based สำหรับมือถือ) -->
        @forelse($transfers as $transfer)
            <div class="card mb-3 border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid var(--bs-success) !important;">
                <div class="card-body p-3 p-md-4">
                    <div class="row align-items-center">
                        <!-- ข้อมูลหลัก -->
                        <div class="col-12 col-md-8">
                            <!-- ทะเบียนรถ (ใหญ่เด่น) -->
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-dark text-white px-3 py-2 fs-6 me-2" style="border-radius: 8px; letter-spacing: 1px;">
                                    <i class="bi bi-truck me-1"></i> {{ $transfer->license_plate }}
                                </span>
                                <span class="badge bg-warning text-dark px-2 py-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-clock"></i> รอตรวจรับ
                                </span>
                            </div>

                            <!-- รายละเอียด -->
                            <div class="row g-2 mt-1">
                                <div class="col-6 col-md-auto">
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">เลขที่รายการ</small>
                                    <span class="fw-semibold text-success" style="font-size: 0.9rem;">{{ $transfer->transfer_no }}</span>
                                </div>
                                <div class="col-6 col-md-auto">
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">ฟาร์มต้นทาง</small>
                                    <span class="fw-medium" style="font-size: 0.9rem;"><i class="bi bi-building text-muted me-1"></i>{{ $transfer->farm->name }}</span>
                                </div>
                                <div class="col-6 col-md-auto">
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">น้ำหนัก</small>
                                    <span class="fw-bold text-primary" style="font-size: 0.9rem;">{{ number_format($transfer->weight, 2) }} <small class="fw-normal text-muted">กก.</small></span>
                                </div>
                                <div class="col-6 col-md-auto">
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">เวลาออกจากฟาร์ม</small>
                                    <span style="font-size: 0.9rem;"><i class="bi bi-clock-history text-muted me-1"></i>{{ $transfer->out_datetime->format('d/m/Y H:i น.') }}</span>
                                </div>
                            </div>

                            <!-- ผู้บันทึก -->
                            <div class="mt-2">
                                <small class="text-muted"><i class="bi bi-person me-1"></i>ผู้บันทึกออก: {{ $transfer->outUser->name }}</small>
                            </div>
                        </div>

                        <!-- ปุ่มตรวจรับ -->
                        <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
                            <button class="btn btn-success w-100 w-md-auto px-4 py-2 rounded-pill btn-receive shadow-sm" 
                                    data-id="{{ $transfer->id }}"
                                    data-no="{{ $transfer->transfer_no }}"
                                    data-farm="{{ $transfer->farm->name }}"
                                    data-vehicle="{{ $transfer->license_plate }}"
                                    data-weight="{{ number_format($transfer->weight, 2) }}"
                                    data-time="{{ $transfer->out_datetime->format('d/m/Y H:i น.') }}"
                                    data-photo="{{ $transfer->out_photo_url }}"
                                    data-remark="{{ $transfer->remark ?? '-' }}">
                                <i class="bi bi-check-circle me-1"></i> ตรวจรับเข้ากอง
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3 text-success opacity-50"></i>
                    <h5 class="fw-semibold text-muted">ไม่มีรายการรถที่รอตรวจรับ</h5>
                    <p class="mb-0 small">เมื่อมีการบันทึกรถขนมูลไก่ออกจากฟาร์ม รายการจะปรากฏที่นี่โดยอัตโนมัติ</p>
                </div>
            </div>
        @endforelse

        @if($transfers->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $transfers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal ตรวจรับเข้ากองปลายทาง -->
<div class="modal fade" id="receiveModal" tabindex="-1" aria-labelledby="receiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--border-radius-lg); overflow: hidden; border: none;">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="receiveModalLabel"><i class="bi bi-box-arrow-in-down-left me-2"></i>ตรวจรับเข้ากองมูลไก่ปลายทาง</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="receiveForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <!-- รายละเอียดขาออกเดิม -->
                    <h6 class="text-success border-bottom pb-2 mb-3 fw-bold"><i class="bi bi-info-circle me-1"></i>รายละเอียดขาออกจากฟาร์ม</h6>
                    
                    <div class="row g-3 mb-4 bg-light p-3 rounded border">
                        <div class="col-md-6 col-12">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">เลขที่รายการ:</span>
                            <span class="fw-semibold text-dark fs-6" id="detail-no"></span>
                        </div>
                        <div class="col-md-6 col-12">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">ฟาร์มต้นทาง:</span>
                            <span class="fw-semibold text-dark fs-6" id="detail-farm"></span>
                        </div>
                        <div class="col-md-4 col-6">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">ทะเบียนรถ:</span>
                            <span class="fw-semibold text-dark fs-6" id="detail-vehicle"></span>
                        </div>
                        <div class="col-md-4 col-6">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">น้ำหนัก:</span>
                            <span class="fw-bold text-success fs-6" id="detail-weight"></span> <span class="text-muted fs-7">กก.</span>
                        </div>
                        <div class="col-md-4 col-12">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">วันเวลาออก:</span>
                            <span class="fw-semibold text-dark fs-6" id="detail-time"></span>
                        </div>
                        <div class="col-12 border-top pt-2">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">หมายเหตุขาออก:</span>
                            <span class="text-dark" id="detail-remark"></span>
                        </div>
                        <div class="col-12 mt-3 text-center">
                            <span class="text-muted d-block text-start mb-2" style="font-size: 0.85rem;">รูปถ่ายหลักฐานขาออก:</span>
                            <img id="detail-photo" src="#" alt="Outward proof photo" class="img-fluid rounded shadow-sm" style="max-height: 180px;">
                            <div id="detail-photo-empty" class="text-muted small d-none mt-2">ไม่พบรูปหลักฐานขาออก</div>
                        </div>
                    </div>

                    <!-- ฟอร์มกรอกรับเข้ากอง -->
                    <h6 class="text-success border-bottom pb-2 mb-3 fw-bold"><i class="bi bi-pencil-square me-1"></i>บันทึกการรับเข้ากองปลายทาง</h6>
                    
                    <div class="row g-3">
                        <!-- เลือกกองมูลไก่ -->
                        <div class="col-md-6 col-12">
                            <label for="pile_id" class="form-label fw-semibold">กองมูลไก่ปลายทางที่ลง <span class="text-danger">*</span></label>
                            <select class="form-select border-success fw-medium" id="pile_id" name="pile_id" required>
                                <option value="" disabled selected>-- เลือกกองมูลไก่ (กอง 1-22) --</option>
                                @foreach($piles as $pile)
                                    <option value="{{ $pile->id }}">{{ $pile->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- วันเวลารับเข้า -->
                        <div class="col-md-6 col-12">
                            <label for="received_datetime" class="form-label fw-semibold">วันเวลารับเข้ากอง <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control border-success" 
                                   id="received_datetime" name="received_datetime" value="{{ $currentDateTime }}" required>
                        </div>

                        <!-- อัปโหลดรูปยืนยันรับเข้า -->
                        <div class="col-12 mt-3">
                            <label class="form-label fw-semibold">รูปถ่ายหลักฐานขารับเข้า <span class="text-danger">*</span></label>
                            
                            <!-- โหมดการเลือก: อัปโหลด หรือ ถ่ายภาพ -->
                            <div class="btn-group w-100 mb-3" role="group" aria-label="Inbound Photo Input Mode">
                                <input type="radio" class="btn-check" name="in_photo_mode" id="in_photo_mode_upload" value="upload" checked autocomplete="off">
                                <label class="btn btn-outline-success border-success" for="in_photo_mode_upload">
                                    <i class="bi bi-folder2-open me-1"></i> อัปโหลดรูปภาพ
                                </label>
                                <input type="radio" class="btn-check" name="in_photo_mode" id="in_photo_mode_camera" value="camera" autocomplete="off">
                                <label class="btn btn-outline-success border-success" for="in_photo_mode_camera">
                                    <i class="bi bi-camera me-1"></i> ถ่ายรูปภาพสดจากแอป
                                </label>
                            </div>

                            <!-- ส่วนที่ 1: อัปโหลดรูปภาพปกติ -->
                            <div id="in-upload-panel" class="mb-3">
                                <input type="file" class="form-control border-success" 
                                       id="receive_photo" name="receive_photo" accept="image/*" capture="environment" onchange="previewInImage(event)" required>
                                <small class="form-text text-muted">รองรับไฟล์ jpg, jpeg, png, webp ขนาดไม่เกิน 5MB (กดเพื่อถ่ายภาพหรือเลือกไฟล์)</small>
                            </div>

                            <!-- ส่วนที่ 2: ถ่ายรูปภาพสดในแอป -->
                            <div id="in-camera-panel" class="d-none mb-3 p-3 border rounded bg-dark">
                                <div class="position-relative overflow-hidden rounded bg-black d-flex align-items-center justify-content-center" style="aspect-ratio: 4/3; min-height: 180px;">
                                    <video id="in-webcam-stream" class="w-100 h-100 d-none" style="object-fit: cover;" autoplay playsinline></video>
                                    <canvas id="in-webcam-canvas" class="d-none"></canvas>
                                    
                                    <!-- ข้อความแจ้งเตือน / สถานะ -->
                                    <div id="in-camera-placeholder" class="text-center text-white p-3">
                                        <i class="bi bi-camera fs-1 text-success d-block mb-2"></i>
                                        <span class="d-block fw-medium small">กล้องยังไม่ได้เปิดใช้งาน</span>
                                        <small class="text-muted" style="font-size: 0.75rem;">กดปุ่ม "เปิดใช้งานกล้อง" ด้านล่างเพื่อเริ่มถ่ายภาพ</small>
                                    </div>
                                </div>
                                
                                <!-- ปุ่มควบคุมกล้อง -->
                                <div class="mt-3 d-flex justify-content-center gap-2 flex-wrap">
                                    <button type="button" id="btn-in-camera-start" class="btn btn-success btn-sm">
                                        <i class="bi bi-camera-video-fill me-1"></i> เปิดใช้งานกล้อง
                                    </button>
                                    <button type="button" id="btn-in-camera-snap" class="btn btn-primary btn-sm d-none">
                                        <i class="bi bi-camera-fill me-1"></i> ถ่ายรูป (Capture)
                                    </button>
                                    <button type="button" id="btn-in-camera-switch" class="btn btn-warning btn-sm d-none">
                                        <i class="bi bi-arrow-repeat me-1"></i> สลับกล้อง
                                    </button>
                                    <button type="button" id="btn-in-camera-stop" class="btn btn-danger btn-sm d-none">
                                        <i class="bi bi-camera-video-off-fill me-1"></i> ปิดกล้อง
                                    </button>
                                </div>
                            </div>

                            <!-- Preview container inside modal -->
                            <div class="mt-3 text-center bg-light p-2 rounded border" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                <img id="in-photo-preview" src="#" alt="Preview" class="img-fluid rounded d-none" style="max-height: 150px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                <span id="in-preview-placeholder" class="text-muted"><i class="bi bi-image fs-4 d-block"></i>ยังไม่ได้เลือกหรือถ่ายรูปถ่ายยืนยัน</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success px-4" onclick="return confirm('กรุณายืนยันความถูกต้องของข้อมูลขารับเข้ากองปลายทาง?');">
                        <i class="bi bi-check-circle me-1"></i> ยืนยันการรับเข้ากอง
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const receiveModal = new bootstrap.Modal(document.getElementById('receiveModal'));
        const receiveForm = document.getElementById('receiveForm');
        
        // Modal detail placeholders
        const detailNo = document.getElementById('detail-no');
        const detailFarm = document.getElementById('detail-farm');
        const detailVehicle = document.getElementById('detail-vehicle');
        const detailWeight = document.getElementById('detail-weight');
        const detailTime = document.getElementById('detail-time');
        const detailRemark = document.getElementById('detail-remark');
        const detailPhoto = document.getElementById('detail-photo');
        const detailPhotoEmpty = document.getElementById('detail-photo-empty');

        // Buttons trigger modal details
        document.querySelectorAll('.btn-receive').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                
                // Populate details
                detailNo.textContent = this.getAttribute('data-no');
                detailFarm.textContent = this.getAttribute('data-farm');
                detailVehicle.textContent = this.getAttribute('data-vehicle');
                detailWeight.textContent = this.getAttribute('data-weight');
                detailTime.textContent = this.getAttribute('data-time');
                detailRemark.textContent = this.getAttribute('data-remark');
                const photoUrl = this.getAttribute('data-photo');
                detailPhoto.classList.remove('d-none');
                detailPhotoEmpty.classList.add('d-none');
                detailPhoto.src = photoUrl || '#';

                // Set form action route dynamically
                receiveForm.action = `/transfers/in/${id}/receive`;

                // Reset modal file fields and previews
                document.getElementById('receive_photo').value = '';
                document.getElementById('in-photo-preview').classList.add('d-none');
                document.getElementById('in-preview-placeholder').classList.remove('d-none');
                document.getElementById('pile_id').value = '';

                // Open modal
                receiveModal.show();
            });
        });

        detailPhoto.addEventListener('error', function() {
            detailPhoto.classList.add('d-none');
            detailPhotoEmpty.classList.remove('d-none');
        });
    });

    function previewInImage(event) {
        const reader = new FileReader();
        const preview = document.getElementById('in-photo-preview');
        const placeholder = document.getElementById('in-preview-placeholder');
        
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

    // ส่วนสคริปต์สตรีมกล้องฝั่งตรวจรับเข้ากอง
    let inCameraStream = null;
    let inFacingMode = 'environment'; // 'environment' = หลัง, 'user' = หน้า

    // สลับโหมดการป้อนรูปภาพ
    document.querySelectorAll('input[name="in_photo_mode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const uploadPanel = document.getElementById('in-upload-panel');
            const cameraPanel = document.getElementById('in-camera-panel');
            const fileInput = document.getElementById('receive_photo');
            
            if (this.value === 'camera') {
                uploadPanel.classList.add('d-none');
                cameraPanel.classList.remove('d-none');
                fileInput.removeAttribute('required'); // ปลดเพื่อยอมรับไฟล์จากการกดถ่ายสด
            } else {
                uploadPanel.classList.remove('d-none');
                cameraPanel.classList.add('d-none');
                fileInput.setAttribute('required', 'required');
                stopInWebcam();
            }
        });
    });

    // เริ่มกล้องตรวจรับ
    async function startInWebcam() {
        const video = document.getElementById('in-webcam-stream');
        const placeholder = document.getElementById('in-camera-placeholder');
        
        const btnStart = document.getElementById('btn-in-camera-start');
        const btnSnap = document.getElementById('btn-in-camera-snap');
        const btnSwitch = document.getElementById('btn-in-camera-switch');
        const btnStop = document.getElementById('btn-in-camera-stop');

        if (inCameraStream) {
            stopInWebcam();
        }

        try {
            placeholder.innerHTML = `<div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div><span class="d-block mt-2 small">กำลังเปิดกล้อง...</span>`;
            
            const constraints = {
                video: {
                    facingMode: { ideal: inFacingMode },
                    width: { ideal: 1280 },
                    height: { ideal: 960 }
                },
                audio: false
            };

            inCameraStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = inCameraStream;
            video.classList.remove('d-none');
            placeholder.classList.add('d-none');

            btnStart.classList.add('d-none');
            btnSnap.classList.remove('d-none');
            btnStop.classList.remove('d-none');

            // ตรวจสอบว่าสามารถสลับกล้องได้หรือไม่
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(device => device.kind === 'videoinput');
            if (videoDevices.length > 1) {
                btnSwitch.classList.remove('d-none');
            }
        } catch (err) {
            console.error("Inbound camera access error:", err);
            placeholder.innerHTML = `
                <i class="bi bi-exclamation-triangle fs-1 text-danger d-block mb-2"></i>
                <span class="d-block fw-bold text-danger small">ไม่สามารถเปิดกล้องได้</span>
                <small class="text-muted" style="font-size: 0.7rem;">โปรดแน่ใจว่าได้ให้สิทธิ์กล้องในเว็บเบราว์เซอร์แล้ว</small>
            `;
            btnStart.classList.remove('d-none');
            btnSnap.classList.add('d-none');
            btnSwitch.classList.add('d-none');
            btnStop.classList.add('d-none');
        }
    }

    // ปิดกล้องตรวจรับ
    function stopInWebcam() {
        if (inCameraStream) {
            inCameraStream.getTracks().forEach(track => track.stop());
            inCameraStream = null;
        }

        const video = document.getElementById('in-webcam-stream');
        const placeholder = document.getElementById('in-camera-placeholder');

        video.srcObject = null;
        video.classList.add('d-none');
        placeholder.classList.remove('d-none');
        placeholder.innerHTML = `
            <i class="bi bi-camera fs-1 text-success d-block mb-2"></i>
            <span class="d-block fw-medium small">กล้องปิดอยู่</span>
            <small class="text-muted" style="font-size: 0.75rem;">กดปุ่ม "เปิดใช้งานกล้อง" เพื่อเริ่มต้นถ่ายภาพ</small>
        `;

        document.getElementById('btn-in-camera-start').classList.remove('d-none');
        document.getElementById('btn-in-camera-snap').classList.add('d-none');
        document.getElementById('btn-in-camera-switch').classList.add('d-none');
        document.getElementById('btn-in-camera-stop').classList.add('d-none');
    }

    // สลับกล้องหน้า-หลัง ตรวจรับ
    function switchInWebcam() {
        inFacingMode = inFacingMode === 'environment' ? 'user' : 'environment';
        startInWebcam();
    }

    // ถ่ายภาพตรวจรับ
    function snapInPhoto() {
        const video = document.getElementById('in-webcam-stream');
        const canvas = document.getElementById('in-webcam-canvas');
        const preview = document.getElementById('in-photo-preview');
        const placeholder = document.getElementById('in-preview-placeholder');
        const fileInput = document.getElementById('receive_photo');

        if (!inCameraStream) return;

        const width = video.videoWidth || 640;
        const height = video.videoHeight || 480;

        canvas.width = width;
        canvas.height = height;

        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, width, height);

        canvas.toBlob(function(blob) {
            if (blob) {
                const file = new File([blob], "receive_capture_" + new Date().getTime() + ".jpg", { type: "image/jpeg" });
                
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

                stopInWebcam();
            }
        }, 'image/jpeg', 0.9);
    }

    // ลงทะเบียนปุ่มเหตุการณ์กล้องตรวจรับ
    document.getElementById('btn-in-camera-start').addEventListener('click', startInWebcam);
    document.getElementById('btn-in-camera-snap').addEventListener('click', snapInPhoto);
    document.getElementById('btn-in-camera-switch').addEventListener('click', switchInWebcam);
    document.getElementById('btn-in-camera-stop').addEventListener('click', stopInWebcam);

    // ดึง Hook การปิด Modal เพื่อเคลียร์กล้องอัตโนมัติ
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('receiveModal');
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', function () {
                stopInWebcam();
                
                // คืนค่าโหมดกลับไปที่อัปโหลดไฟล์เริ่มต้น
                const modeUpload = document.getElementById('in_photo_mode_upload');
                if (modeUpload) {
                    modeUpload.checked = true;
                    document.getElementById('in-upload-panel').classList.remove('d-none');
                    document.getElementById('in-camera-panel').classList.add('d-none');
                    document.getElementById('receive_photo').setAttribute('required', 'required');
                }
            });
        }
    });

    // หยุดกล้องเพื่อป้องกันสตรีมค้าง
    window.addEventListener('beforeunload', stopInWebcam);
</script>
@endsection
