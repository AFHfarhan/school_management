@extends('themes.master')
@section('page_title', 'Transaction List')
@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Pembayaran</h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Add Transaction (compact) -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Buat Pembayaran Baru</h6>
                </div>
                <div class="card-body">
                    <form method="get" action="{{ route('v1.transaction.create') }}">
                        <div class="form-row align-items-end">
                            <div class="col-md-6 mb-3">
                                <label for="transaction_type">Transaction Type</label>
                                <select name="type" id="transaction_type" class="form-control">
                                    @php
                                        $types = $types ?? ['ppdb' => 'PPDB', 'spp' => 'SPP',  'other' => 'Other'];
                                    @endphp
                                    @foreach($types as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-block">Create</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <!-- Transaction List -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Transaction History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="transactionsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Transaction Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions ?? [] as $i => $t)
                                    @php
                                        $name = data_get($t, 'name', $t->name ?? '');
                                        $type = data_get($t, 'type', $t->type ?? '');
                                        $status = data_get($t, 'status', $t->status ?? '');
                                        $created = data_get($t, 'created_at', $t->created_at ?? data_get($t, 'created_date', ''));
                                        $payment = data_get($t, 'payment_date', $t->payment_date ?? '');
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $name }}</td>
                                        <td>{{ ucfirst($type) }}</td>
                                        <td>{{ ucfirst($status) }}</td>
                                        <td>{{ $created }}</td>
                                        <td>{{ $payment }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function(){
        $('#transactionsTable').DataTable();
        // init flatpickr for payment date
        if (typeof flatpickr !== 'undefined') {
            flatpickr('.flatpickr', { dateFormat: 'Y-m-d' });
        }
    });
</script>
@endsection
