<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานการขนย้ายมูลไก่</title>
    
    @empty($pdfMode)
    <!-- Google Fonts: Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS for base grid -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endempty
    
    <style>
        body {
            font-family: {{ empty($pdfMode) ? "'Kanit', sans-serif" : "garuda, sans-serif" }};
            color: #333333;
            background-color: #ffffff;
            font-size: 0.8rem;
            padding: 1.5cm;
        }

        .screen-toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin: -1.5cm -1.5cm 1.2rem;
            padding: 12px 1.5cm;
            background: #ffffff;
            border-bottom: 1px solid #dfe7df;
            box-shadow: 0 6px 20px rgba(30, 70, 32, 0.08);
        }

        .screen-toolbar-title {
            font-weight: 700;
            color: #1e4620;
        }

        .screen-toolbar-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .toolbar-btn {
            border: 1px solid #2e7d32;
            border-radius: 999px;
            padding: 8px 16px;
            font-weight: 600;
            text-decoration: none;
            background: #ffffff;
            color: #2e7d32;
            cursor: pointer;
        }

        .toolbar-btn-primary {
            background: #2e7d32;
            color: #ffffff;
        }

        .toolbar-btn:hover {
            opacity: 0.9;
        }

        .header-title {
            text-align: center;
            margin-bottom: 2rem;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .py-4 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #6c757d;
        }

        .fw-semibold {
            font-weight: 600;
        }

        .print-logo {
            max-width: 210px;
            width: 100%;
            height: auto;
            margin: 0 auto 1rem;
            display: block;
        }

        .header-title h4 {
            font-weight: 700;
            color: #1e4620;
            margin-bottom: 0.5rem;
        }

        .header-title p {
            color: #666;
            font-size: 0.9rem;
        }

        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .print-table th {
            background-color: #f2f2f2 !important;
            color: #1e4620;
            font-weight: 600;
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }

        .print-table td {
            border: 1px solid #dddddd;
            padding: 8px;
            vertical-align: middle;
        }

        .proof-img {
            max-height: 55px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .transfer-no-cell {
            white-space: nowrap;
        }

        .badge-print {
            border: 1px solid #666;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            display: inline-block;
        }

        .badge-pending {
            border-color: #ef6c00;
            color: #ef6c00;
        }

        .badge-received {
            border-color: #2e7d32;
            color: #2e7d32;
        }

        .badge-cancelled {
            border-color: #c62828;
            color: #c62828;
        }

        @media print {
            body {
                padding: 0;
            }

            .screen-toolbar {
                display: none !important;
            }

            @page {
                size: A4 landscape;
                margin: 1cm;
            }
        }
    </style>
</head>
<body>

    @php
        $pdfMode = $pdfMode ?? false;
        $logoSrc = $pdfMode ? $logoPath : asset('images/cfarm-logo.png');
    @endphp

    @empty($pdfMode)
    <div class="screen-toolbar">
        <div class="screen-toolbar-title">รายงาน PDF</div>
        <div class="screen-toolbar-actions">
            <a href="{{ route('reports.pdf', request()->query()) }}" class="toolbar-btn toolbar-btn-primary">ดาวน์โหลด PDF</a>
            <button type="button" class="toolbar-btn" id="printReportBtn">พิมพ์รายงาน</button>
            <a href="{{ route('reports.index', request()->query()) }}" class="toolbar-btn">กลับหน้ารายงาน</a>
        </div>
    </div>
    @endempty

    <div class="header-title">
        <img src="{{ $logoSrc }}" alt="CFARM Logo" class="print-logo">
        <h4>รายงานประวัติการขนย้ายมูลไก่ออกจากฟาร์มและรับเข้ากอง</h4>
        <p>ข้อมูล ณ วันที่ {{ date('d/m/Y H:i') }} น. | จำนวนรายการทั้งหมด {{ $transfers->count() }} เที่ยว</p>
    </div>

    <table class="print-table">
        <thead>
            <tr>
                <th style="width: 140px;">เลขที่รายการ</th>
                <th>ฟาร์มต้นทาง</th>
                <th style="width: 110px;">ทะเบียนรถ</th>
                <th style="width: 90px; text-align: right;">น้ำหนัก (กก.)</th>
                <th>วันเวลาออกฟาร์ม</th>
                <th>วันเวลารับเข้ากอง</th>
                <th>กองที่ลง</th>
                <th style="width: 110px;">สถานะ</th>
                <th style="width: 70px; text-align: center;">รูปออก</th>
                <th style="width: 70px; text-align: center;">รูปรวม</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transfers as $transfer)
                <tr>
                    <td class="fw-semibold transfer-no-cell">{{ $transfer->transfer_no }}</td>
                    <td>{{ $transfer->farm->name }}</td>
                    <td>{{ $transfer->license_plate }}</td>
                    <td style="text-align: right;" class="fw-semibold">{{ number_format($transfer->weight, 2) }}</td>
                    <td>
                        <div>{{ $transfer->out_datetime->format('d/m/Y H:i') }} น.</div>
                        <small class="text-muted" style="font-size: 0.7rem;">โดย: {{ $transfer->outUser->name }}</small>
                    </td>
                    <td>
                        @if($transfer->received_datetime)
                            <div>{{ $transfer->received_datetime->format('d/m/Y H:i') }} น.</div>
                            <small class="text-muted" style="font-size: 0.7rem;">โดย: {{ $transfer->receiveUser ? $transfer->receiveUser->name : '-' }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $transfer->pile ? $transfer->pile->name : '-' }}</td>
                    <td>
                        @if($transfer->status === \App\Models\ManureTransfer::STATUS_PENDING)
                            <span class="badge-print badge-pending">รอรับเข้ากอง</span>
                        @elseif($transfer->status === \App\Models\ManureTransfer::STATUS_RECEIVED)
                            <span class="badge-print badge-received">รับเข้ากองแล้ว</span>
                        @else
                            <span class="badge-print badge-cancelled">ยกเลิก</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @php
                            $outPhotoSrc = $pdfMode && $transfer->out_photo
                                ? \Illuminate\Support\Facades\Storage::disk('public')->path($transfer->out_photo)
                                : $transfer->out_photo_url;
                        @endphp
                        <img src="{{ $outPhotoSrc }}" alt="Out" class="proof-img">
                    </td>
                    <td class="text-center">
                        @if($transfer->receive_photo)
                            @php
                                $receivePhotoSrc = $pdfMode
                                    ? \Illuminate\Support\Facades\Storage::disk('public')->path($transfer->receive_photo)
                                    : $transfer->receive_photo_url;
                            @endphp
                            <img src="{{ $receivePhotoSrc }}" alt="In" class="proof-img">
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center py-4">ไม่พบรายการขนย้ายตามตัวกรองที่เลือก</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <small class="text-muted">พิมพ์โดยระบบแอปพลิเคชันจัดการฟาร์มมูลไก่ | วันเวลาพิมพ์: {{ date('d/m/Y H:i:s') }}</small>
        <small class="text-muted">ลงชื่อผู้ตรวจสอบ....................................................</small>
    </div>

    <!-- Automatically open browser print dialog on load -->
    @empty($pdfMode)
    <script>
        function openPdfDialog() {
            window.print();
        }

        document.getElementById('printReportBtn').addEventListener('click', openPdfDialog);

        window.onload = function() {
            setTimeout(function() {
                openPdfDialog();
            }, 500);
        };
    </script>
    @endempty
</body>
</html>
