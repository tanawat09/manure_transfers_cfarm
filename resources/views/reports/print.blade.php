<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานการขนย้ายมูลไก่</title>
    
    <!-- Google Fonts: Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS for base grid -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            color: #333333;
            background-color: #ffffff;
            font-size: 0.8rem;
            padding: 1.5cm;
        }

        .header-title {
            text-align: center;
            margin-bottom: 2rem;
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
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
        }
    </style>
</head>
<body>

    <div class="header-title">
        <img src="{{ asset('images/cfarm-logo.png') }}" alt="CFARM Logo" class="print-logo">
        <h4>รายงานประวัติการขนย้ายมูลไก่ออกจากฟาร์มและรับเข้ากอง</h4>
        <p>ข้อมูล ณ วันที่ {{ date('d/m/Y H:i') }} น. | จำนวนรายการทั้งหมด {{ $transfers->count() }} เที่ยว</p>
    </div>

    <table class="print-table">
        <thead>
            <tr>
                <th style="width: 110px;">เลขที่รายการ</th>
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
                    <td class="fw-semibold">{{ $transfer->transfer_no }}</td>
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
                        <img src="{{ asset('storage/' . $transfer->out_photo) }}" alt="Out" class="proof-img">
                    </td>
                    <td class="text-center">
                        @if($transfer->receive_photo)
                            <img src="{{ asset('storage/' . $transfer->receive_photo) }}" alt="In" class="proof-img">
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
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
