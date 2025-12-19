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
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="m-0 font-weight-bold text-primary">Transaction Information</h6>
                        </div>
                        <div class="col-md-6 text-right">
                            @if($transaction->data['status'] === 'pending')
                                <a href="{{ route('v1.transaction.updatePayment', $transaction->id) }}" class="btn btn-success">
                                    <i class="fas fa-money-bill"></i> Update Payment
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmCancelPayment({{ $transaction->id }})">
                                    <i class="fas fa-times-circle"></i> Cancel Payment
                                </button>
                            @endif
                            <button onclick="window.print()" class="btn btn-primary">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
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
                            @if(isset($data['payment_date']))
                                <tr>
                                    <th width="30%">Payment Date</th>
                                    <td>{{ $data['payment_date'] }}</td>
                                </tr>
                            @endif
                            @php
                                $additionalData = is_array($transaction->additional_data) ? $transaction->additional_data : json_decode($transaction->additional_data, true);
                            @endphp
                            @if(isset($additionalData['payments']) && is_array($additionalData['payments']) && count($additionalData['payments']) > 0)
                                <tr>
                                    <th>Payment Evidence</th>
                                    <td>
                                        @foreach($additionalData['payments'] as $payment)
                                            <div class="mb-3">
                                                <h6 class="font-weight-bold mb-2">Payment #{{ $loop->iteration }}</h6>
                                                <p class="mb-1"><strong>Date:</strong> {{ $payment['payment_date'] ?? '-' }}</p>
                                                <p class="mb-2"><strong>Uploaded by:</strong> {{ $payment['uploaded_by'] ?? '-' }} ({{ $payment['uploaded_at'] ?? '-' }})</p>
                                                @if(isset($payment['evidence']) && is_array($payment['evidence']))
                                                    <div class="row">
                                                        @foreach($payment['evidence'] as $evidence)
                                                            <div class="col-md-3 mb-2">
                                                                <a href="{{ asset('uploads/payment_evidence/' . $evidence) }}" target="_blank">
                                                                    <img src="{{ asset('uploads/payment_evidence/' . $evidence) }}" alt="Payment Evidence" style="width: 100%; max-height: 200px; object-fit: cover;" class="img-thumbnail">
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if(!$loop->last)
                                                    <hr>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                </tr>
                            @endif
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

                    @if($status === 'pending')
                        <div class="alert alert-info mt-4">
                            <h6 class="font-weight-bold mb-3"><i class="fas fa-university"></i> Bank Account Information for Payment</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td width="30%"><strong>Bank Name</strong></td>
                                        <td>:</td>
                                        <td>{{ $bankInfo['bank_name'] ?? 'BCA (Bank Central Asia)' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Account Number</strong></td>
                                        <td>:</td>
                                        <td><code>{{ $bankInfo['account_number'] ?? '1234567890' }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Account Name</strong></td>
                                        <td>:</td>
                                        <td>{{ $bankInfo['account_name'] ?? 'SMK SEKOLAH MANAGEMENT' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Amount</strong></td>
                                        <td>:</td>
                                        <td><span class="badge badge-primary">Rp {{ $data['amount'] ?? '-' }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="mt-3 mb-0 small text-muted">Please transfer the exact amount and upload the payment evidence to update the status to PAID.</p>
                        </div>
                    @endif
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

<!-- Hidden form for cancel payment -->
<form id="cancelPaymentForm" method="POST" style="display: none;">
    @csrf
    @method('POST')
</form>

<script>
    function confirmCancelPayment(transactionId) {
        if (confirm('Are you sure you want to cancel this payment? This action will change the status to CANCELLED.')) {
            document.getElementById('cancelPaymentForm').action = '/v1/transactions/' + transactionId + '/cancel-payment';
            document.getElementById('cancelPaymentForm').submit();
        }
    }
</script>

@endsection
