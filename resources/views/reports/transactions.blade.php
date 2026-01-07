@extends('themes.master')
@section('page_title', 'Laporan Pembayaran')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3 text-gray-800">Laporan Pembayaran</h1>

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Transaksi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totals['total'], 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Dibayar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totals['paid'], 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('v1.reports.transactions') }}">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Kategori</label>
                        <select name="category" class="form-control">
                            <option value="">Semua</option>
                            <option value="ppdb" {{ $filters['category'] === 'ppdb' ? 'selected' : '' }}>PPDB</option>
                            <option value="spp" {{ $filters['category'] === 'spp' ? 'selected' : '' }}>SPP</option>
                            <option value="other" {{ $filters['category'] === 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua</option>
                            <option value="paid" {{ $filters['status'] === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ $filters['status'] === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="cancelled" {{ $filters['status'] === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Terapkan</button>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data ({{ $transactions->count() }})</h6>
            <button class="btn btn-success" data-toggle="modal" data-target="#exportTransactionModal">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="transactionReportTable" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tgl Bayar</th>
                            <th>Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $index => $transaction)
                            @php $data = is_array($transaction->data) ? $transaction->data : []; @endphp
                            @php $status = $data['status'] ?? 'pending'; @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $transaction->name }}</td>
                                <td>{{ strtoupper($transaction->category) }}</td>
                                <td>Rp {{ number_format((float) ($data['amount'] ?? 0), 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $badge = 'secondary';
                                        if ($status === 'paid') $badge = 'success';
                                        elseif ($status === 'pending') $badge = 'warning';
                                        elseif ($status === 'cancelled') $badge = 'danger';
                                    @endphp
                                    <span class="badge badge-{{ $badge }}">{{ ucfirst($status) }}</span>
                                </td>
                                <td>{{ $data['payment_date'] ?? '' }}</td>
                                <td>{{ optional($transaction->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exportTransactionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Kolom</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('v1.reports.transactions.export') }}">
                @csrf
                <input type="hidden" name="start_date" value="{{ $filters['start_date'] }}">
                <input type="hidden" name="end_date" value="{{ $filters['end_date'] }}">
                <input type="hidden" name="category" value="{{ $filters['category'] }}">
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
                <div class="modal-body">
                    <div class="row">
                        @foreach($availableColumns as $key => $label)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="columns[]" value="{{ $key }}" id="trx_col_{{ $loop->index }}" {{ in_array($key, $defaultColumns) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="trx_col_{{ $loop->index }}">{{ $label }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-download"></i> Download</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#transactionReportTable').DataTable({
            order: [[6, 'desc']]
        });
    });
</script>
@endpush
