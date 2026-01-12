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
                    <form method="get" action="{{ route('v1.transaction.create') }}" id="createTransactionForm">
                        <div class="form-row align-items-end">
                            <div class="col-md-4 mb-3">
                                <label for="transaction_type">Transaction Type</label>
                                <select name="type" id="transaction_type" class="form-control">
                                    @php
                                        $types = $types ?? ['ppdb' => 'PPDB', 'spp' => 'SPP',  'other' => 'Other'];
                                    @endphp
                                    @foreach($types as $key => $label)
                                        <option value="{{ $key }}" data-title="{{ $types[$key] ?? '' }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3" id="title_field_container" style="display: none;">
                                <label for="transaction_title_input">Transaction Title</label>
                                <input type="text" name="title" id="transaction_title_input" class="form-control" placeholder="Enter transaction title">
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
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Payment Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions ?? [] as $i => $t)
                                    @php
                                        $name = data_get($t, 'name', $t->name ?? '');
                                        $category = data_get($t, 'category', $t->category ?? '');
                                        $data = is_array($t->data) ? $t->data : json_decode($t->data, true);
                                        $status = $data['status'] ?? 'pending';
                                        $created = $t->created_at ? $t->created_at->format('d/m/Y H:i:s') : '';
                                        $payment = $data['payment_date'] ?? '';
                                        $badgeClass = $status === 'paid' ? 'success' : ($status === 'failed' ? 'danger' : ($status === 'cancelled' ? 'secondary' : 'warning'));
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $name }}</td>
                                        <td>{{ strtoupper($category) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                        </td>
                                        <td>{{ $created }}</td>
                                        <td>{{ $payment }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('v1.transaction.show', $t->id) }}" class="btn btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('v1.transaction.updatePayment', $t->id) }}" class="btn btn-success" title="Update Payment">
                                                    <i class="fas fa-money-bill"></i>
                                                </a>
                                            </div>
                                        </td>
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
        // Show/hide title field based on transaction type
        $('#transaction_type').on('change', function() {
            var selectedValue = $(this).val().toLowerCase();
            console.log('Selected value:', selectedValue); // Debug log
            if (selectedValue === 'other') {
                $('#title_field_container').show();
                $('#transaction_title_input').prop('required', true);
            } else {
                $('#title_field_container').hide();
                $('#transaction_title_input').prop('required', false);
                $('#transaction_title_input').val('');
            }
        });
        
        // Set initial value after binding event
        $('#transaction_type').trigger('change');

        // Initialize DataTable only if table exists
        if ($('#transactionsTable').length) {
            $('#transactionsTable').DataTable();
        }
        
        // init flatpickr for payment date
        if (typeof flatpickr !== 'undefined') {
            flatpickr('.flatpickr', { dateFormat: 'Y-m-d' });
        }
    });
</script>
@endsection

