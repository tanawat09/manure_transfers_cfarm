<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานการขนย้ายมูลไก่</title>
    <style>
        body {
            font-family: garuda, sans-serif;
            color: #222222;
            font-size: 10pt;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .logo {
            width: 125px;
            margin-bottom: 8px;
        }

        h1 {
            color: #1e4620;
            font-size: 18pt;
            margin: 0 0 4px;
        }

        .meta {
            color: #555555;
            font-size: 10pt;
            margin-bottom: 8px;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .summary td {
            border: 1px solid #cfd8cf;
            padding: 6px 8px;
            background: #f7fbf7;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .report-table th {
            background: #e8f5e9;
            color: #1e4620;
            border: 1px solid #9fb59f;
            padding: 6px 5px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .report-table td {
            border: 1px solid #cfcfcf;
            padding: 5px;
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .nowrap {
            white-space: nowrap;
        }

        .small {
            color: #666666;
            font-size: 8.5pt;
        }

        .status {
            border-radius: 4px;
            padding: 2px 5px;
            display: inline-block;
            font-size: 8.5pt;
            border: 1px solid #777777;
        }

        .status-pending {
            color: #d97706;
            border-color: #d97706;
        }

        .status-received {
            color: #15803d;
            border-color: #15803d;
        }

        .status-cancelled {
            color: #b91c1c;
            border-color: #b91c1c;
        }

        .footer {
            margin-top: 12px;
            color: #666666;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    @php
        $totalWeight = $transfers->sum('weight');
    @endphp

    <div class="header">
        <img src="{{ $logoPath }}" class="logo" alt="CFARM">
        <h1>รายงานประวัติการขนย้ายมูลไก่</h1>
        <div class="meta">พิมพ์วันที่ {{ now()->format('d/m/Y H:i') }} น.</div>
    </div>

    <table class="summary">
        <tr>
            <td><strong>จำนวนรายการ:</strong> {{ number_format($transfers->count()) }} เที่ยว</td>
            <td><strong>น้ำหนักรวม:</strong> {{ number_format($totalWeight, 2) }} กก.</td>
            <td><strong>น้ำหนักรวม:</strong> {{ number_format($totalWeight / 1000, 2) }} ตัน</td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 11%;">เลขที่รายการ</th>
                <th style="width: 13%;">ฟาร์มต้นทาง</th>
                <th style="width: 9%;">ทะเบียนรถ</th>
                <th style="width: 9%;">น้ำหนัก<br>(กก.)</th>
                <th style="width: 13%;">วันเวลาออก</th>
                <th style="width: 13%;">วันเวลารับเข้า</th>
                <th style="width: 10%;">กองปลายทาง</th>
                <th style="width: 10%;">สถานะ</th>
                <th style="width: 6%;">รูปออก</th>
                <th style="width: 6%;">รูปรับ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transfers as $transfer)
                <tr>
                    <td class="nowrap">{{ $transfer->transfer_no }}</td>
                    <td>{{ $transfer->farm->name }}</td>
                    <td class="text-center nowrap">{{ $transfer->license_plate }}</td>
                    <td class="text-right nowrap">{{ number_format($transfer->weight, 2) }}</td>
                    <td>
                        {{ $transfer->out_datetime->format('d/m/Y H:i') }} น.
                        <div class="small">โดย: {{ $transfer->outUser->name }}</div>
                    </td>
                    <td>
                        @if($transfer->received_datetime)
                            {{ $transfer->received_datetime->format('d/m/Y H:i') }} น.
                            <div class="small">โดย: {{ $transfer->receiveUser ? $transfer->receiveUser->name : '-' }}</div>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $transfer->pile ? $transfer->pile->name : '-' }}</td>
                    <td class="text-center">
                        @if($transfer->status === \App\Models\ManureTransfer::STATUS_PENDING)
                            <span class="status status-pending">รอรับเข้า</span>
                        @elseif($transfer->status === \App\Models\ManureTransfer::STATUS_RECEIVED)
                            <span class="status status-received">รับแล้ว</span>
                        @else
                            <span class="status status-cancelled">ยกเลิก</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $transfer->out_photo ? 'มี' : '-' }}</td>
                    <td class="text-center">{{ $transfer->receive_photo ? 'มี' : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">ไม่พบข้อมูลตามเงื่อนไขที่เลือก</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        พิมพ์โดยระบบรายงาน CFARM | ลงชื่อผู้ตรวจสอบ ....................................................
    </div>
</body>
</html>
