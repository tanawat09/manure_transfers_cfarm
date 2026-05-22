@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'ภาพรวมการขนย้ายมูลไก่')

@section('content')
<div class="d-flex flex-column gap-3 mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h4 class="mb-1 text-success fw-bold">
                @if($isCustomRange)
                    สรุปตามช่วงวันที่ที่เลือก
                @else
                    สรุปย้อนหลัง {{ number_format($selectedPeriod) }} วัน
                @endif
            </h4>
            <p class="text-muted mb-0">
                ช่วงวันที่ {{ $periodStart->format('d/m/Y') }} ถึง {{ $todayEnd->format('d/m/Y') }}
            </p>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-wrap gap-2">
            @foreach($allowedPeriods as $period)
                <button
                    type="submit"
                    name="days"
                    value="{{ $period }}"
                    class="btn {{ ! $isCustomRange && $selectedPeriod === $period ? 'btn-success' : 'btn-outline-success' }} rounded-pill px-3"
                >
                    {{ $period }} วัน
                </button>
            @endforeach
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard') }}" class="row g-3 align-items-end">
                <div class="col-md-4 col-12">
                    <label for="from_date" class="form-label fw-medium">จากวันที่</label>
                    <input
                        type="date"
                        id="from_date"
                        name="from_date"
                        class="form-control"
                        value="{{ old('from_date', $fromDate?->format('Y-m-d')) }}"
                    >
                </div>
                <div class="col-md-4 col-12">
                    <label for="to_date" class="form-label fw-medium">ถึงวันที่</label>
                    <input
                        type="date"
                        id="to_date"
                        name="to_date"
                        class="form-control"
                        value="{{ old('to_date', $toDate?->format('Y-m-d')) }}"
                    >
                </div>
                <div class="col-md-4 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-success flex-fill">
                        <i class="bi bi-funnel me-1"></i> ดูตามวันที่
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary flex-fill">
                        ล้างค่า
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4 col-sm-6 col-12">
        <div class="card border-start border-success border-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted d-block mb-1 fw-medium">จำนวนเที่ยวขนออกสะสม</span>
                    <h3 class="mb-0 fw-bold text-success">{{ number_format($outboundTrips) }}</h3>
                    <small class="text-muted">เที่ยว</small>
                </div>
                <div class="bg-success-subtle text-success rounded-3 p-3">
                    <i class="bi bi-truck fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-sm-6 col-12">
        <div class="card border-start border-primary border-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted d-block mb-1 fw-medium">น้ำหนักรวมสะสม</span>
                    <h3 class="mb-0 fw-bold text-primary">{{ number_format($totalWeight, 2) }}</h3>
                    <small class="text-muted">กก.</small>
                </div>
                <div class="bg-primary-subtle text-primary rounded-3 p-3">
                    <i class="bi bi-speedometer2 fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-sm-12 col-12">
        <div class="card border-start border-warning border-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted d-block mb-1 fw-medium">รับเข้ากอง / ค้างรอ</span>
                    <h3 class="mb-0 fw-bold text-warning">{{ number_format($receivedTrips) }} / {{ number_format($pendingTransfers) }}</h3>
                    <small class="text-muted">เที่ยว</small>
                </div>
                <div class="bg-warning-subtle text-warning rounded-3 p-3">
                    <i class="bi bi-hourglass-split fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6 col-12">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <span class="fw-semibold text-success"><i class="bi bi-graph-up-arrow me-2"></i>กราฟน้ำหนักรวมย้อนหลัง</span>
            </div>
            <div class="card-body">
                <canvas id="weightChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-12">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <span class="fw-semibold text-success"><i class="bi bi-bar-chart-steps me-2"></i>กราฟจำนวนเที่ยวย้อนหลัง</span>
            </div>
            <div class="card-body">
                <canvas id="tripChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6 col-12">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <span class="fw-semibold text-success"><i class="bi bi-house me-2"></i>น้ำหนักรวมแยกตามฟาร์ม</span>
            </div>
            <div class="card-body">
                @forelse($farmWeights as $farm)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-medium text-dark">{{ $farm->farm_name }}</span>
                            <span class="badge bg-success-subtle text-success">{{ number_format($farm->total_weight, 2) }} กก.</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div
                                class="progress-bar bg-success"
                                role="progressbar"
                                style="width: {{ $totalWeight > 0 ? ($farm->total_weight / $totalWeight) * 100 : 0 }}%"
                            ></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox d-block fs-3 mb-2"></i>
                        ยังไม่มีข้อมูลในช่วงเวลาที่เลือก
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-12">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <span class="fw-semibold text-success"><i class="bi bi-layers me-2"></i>น้ำหนักรับเข้าแยกตามกอง</span>
            </div>
            <div class="card-body">
                @forelse($pileWeights as $pile)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <span class="fw-medium text-dark">{{ $pile->pile_name }}</span>
                        <span class="badge bg-primary text-white rounded-pill">{{ number_format($pile->total_weight, 2) }} กก.</span>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox d-block fs-3 mb-2"></i>
                        ยังไม่มีข้อมูลรับเข้าในช่วงเวลาที่เลือก
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-semibold text-success"><i class="bi bi-list-stars me-2"></i>รายการล่าสุด 10 รายการ</span>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">ดูทั้งหมด</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">เลขที่รายการ</th>
                                <th>ฟาร์ม</th>
                                <th>ทะเบียนรถ</th>
                                <th>น้ำหนัก</th>
                                <th>วันเวลาออก</th>
                                <th>กองที่ลง</th>
                                <th>สถานะ</th>
                                <th>ผู้บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestTransfers as $transfer)
                                <tr>
                                    <td class="px-4 fw-semibold text-dark">{{ $transfer->transfer_no }}</td>
                                    <td class="text-success fw-medium">{{ $transfer->farm->name }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $transfer->license_plate }}</span></td>
                                    <td class="fw-semibold">{{ number_format($transfer->weight, 2) }}</td>
                                    <td>{{ $transfer->out_datetime->format('d/m/Y H:i') }}</td>
                                    <td>{{ $transfer->pile ? $transfer->pile->name : '-' }}</td>
                                    <td>
                                        @if($transfer->status === \App\Models\ManureTransfer::STATUS_PENDING)
                                            <span class="badge-pending">รอรับเข้ากอง</span>
                                        @elseif($transfer->status === \App\Models\ManureTransfer::STATUS_RECEIVED)
                                            <span class="badge-received">รับเข้ากองแล้ว</span>
                                        @else
                                            <span class="badge-cancelled">ยกเลิก</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="lh-1 text-dark" style="font-size: 0.85rem;">{{ $transfer->outUser->name }}</div>
                                        @if($transfer->receiveUser)
                                            <small class="text-muted">{{ $transfer->receiveUser->name }}</small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        ยังไม่มีรายการขนย้าย
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    const dateLabels = @json($dateLabels);
    const tripSeries = @json($tripSeries);
    const receivedSeries = @json($receivedSeries);
    const weightSeries = @json($weightSeries);

    const commonGrid = {
        color: 'rgba(0, 0, 0, 0.06)'
    };

    new Chart(document.getElementById('weightChart'), {
        type: 'line',
        data: {
            labels: dateLabels,
            datasets: [{
                label: 'น้ำหนักรวม (กก.)',
                data: weightSeries,
                borderColor: '#2e7d32',
                backgroundColor: 'rgba(46, 125, 50, 0.12)',
                fill: true,
                tension: 0.35,
                borderWidth: 3,
                pointRadius: 3,
                pointBackgroundColor: '#2e7d32'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: commonGrid }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    new Chart(document.getElementById('tripChart'), {
        type: 'bar',
        data: {
            labels: dateLabels,
            datasets: [
                {
                    label: 'เที่ยวขนออก',
                    data: tripSeries,
                    backgroundColor: '#1e88e5',
                    borderRadius: 8
                },
                {
                    label: 'เที่ยวรับเข้า',
                    data: receivedSeries,
                    backgroundColor: '#43a047',
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { stacked: false, grid: { display: false } },
                y: { beginAtZero: true, grid: commonGrid }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection
