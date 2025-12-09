@extends('themes.master')
@section('page_title', 'Transaction Detail')
@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 align-items-center">{{ $transaction->name }}</h1>
        <div>
            <a href="{{ route('v1.transaction.index') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> Back to List
            </a>
            <a href="{{ route('v1.transaction.edit', $transaction->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4" id="printable-area">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Transaction Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="30%">Transaction ID</th>
                                <td>{{ 'TR/SMK/'. strtoupper($transaction->category) . "/" . $transaction->id . '/' . $transaction->created_at->format('d/m/y') }}</td>
                            </tr>
                            <tr>
                                <th>Transaction Name</th>
                                <td>{{ $transaction->name }}</td>
                            </tr>
                            <tr>
                                <th>Transaction Category</th>
                                <td>{{ strtoupper($transaction->category ?? $transaction->category ?? '-') }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @php
                                        $data = is_array($transaction->data) ? $transaction->data : json_decode($transaction->data, true);
                                        $status = $data['status'] ?? 'pending';
                                        $badgeClass = $status === 'paid' ? 'success' : ($status === 'failed' ? 'danger' : 'warning');
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Payment Date</th>
                                <td>
                                    @php
                                        $data = is_array($transaction->data) ? $transaction->data : json_decode($transaction->data, true);
                                        $paymentDate = $data['payment_date'] ?? null;
                                    @endphp
                                    {{ $paymentDate ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Created Date</th>
                                <td>{{ $transaction->created_at ? $transaction->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    @if(!empty($transaction->data))
                        @php
                            $data = is_array($transaction->data) ? $transaction->data : json_decode($transaction->data, true);
                        @endphp
                        @if($data && is_array($data))
                            <h6 class="font-weight-bold text-primary mt-4">Transaction Data</h6>
                            <table class="table table-bordered">
                                <tbody>
                                    @if(isset($data['amount']))
                                        <tr>
                                            <th width="30%">Amount</th>
                                            <td>{{ 'Rp ' . $data['amount'] }}</td>
                                        </tr>
                                    @endif
                                    @if(isset($data['amount_terbilang']))
                                        <tr>
                                            <th>Amount in Words</th>
                                            <td>{{ $data['amount_terbilang'] }}</td>
                                        </tr>
                                    @endif
                                    @if(isset($data['payer']))
                                        <tr>
                                            <th>Payer / Customer</th>
                                            <td>{{ $data['payer'] }}</td>
                                        </tr>
                                    @endif
                                    @if(isset($data['recipient']))
                                        <tr>
                                            <th>Recipient</th>
                                            <td>{{ $data['recipient'] }}</td>
                                        </tr>
                                    @endif
                                    @if(isset($data['notes']))
                                        <tr>
                                            <th>Notes</th>
                                            <td>{{ $data['notes'] }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        @endif
                    @endif

                    <h6 class="font-weight-bold text-primary mt-4">Payments Information</h6>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="30%">Created By</th>
                                <td>{{ $transaction->creator->name ?? 'System' }}</td>
                            </tr>
                            @if($transaction->updated_by)
                                <tr>
                                    <th>Updated By</th>
                                    <td>{{ $transaction->updater->name ?? '-' }}</td>
                                </tr>
                            @endif
                            @if($transaction->updated_at && $transaction->updated_at != $transaction->created_at)
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $transaction->updated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    @media print {
        .btn, .sidebar, .topbar, .footer, .no-print {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        body {
            background-color: white !important;
        }
    }
</style>
@endsection
