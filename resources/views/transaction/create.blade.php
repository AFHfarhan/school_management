@extends('themes.master')
@section('page_title', 'Create Transaction')
@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Transaction</h1>
        <a href="{{ route('v1.transaction.index') }}" class="btn btn-secondary">Back to list</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Transaction Details</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('v1.transaction.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="name">Transaction Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="type">Transaction Type</label>
                                @php $selectedType = $preselectedType ?? old('type') ?? null; @endphp
                                <select name="{{ $selectedType ? 'type_select_disabled' : 'type' }}" id="type" class="form-control" {{ $selectedType ? 'disabled' : '' }}>
                                    @php
                                        $types = $types ?? ['tuition' => 'Tuition', 'donation' => 'Donation', 'other' => 'Other'];
                                    @endphp
                                    @foreach($types as $k=>$v)
                                        <option value="{{ $k }}" {{ ($selectedType == $k) ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                                @if($selectedType)
                                    <input type="hidden" name="type" value="{{ $selectedType }}">
                                @else
                                    <input type="hidden" name="type" value="{{ old('type') }}">
                                @endif
                            </div>
                            <div class="form-group col-md-6">
                                <label for="category">Category</label>
                                <input type="text" name="category" id="category" class="form-control" value="{{ old('category') }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="amount">Amount</label>
                                <input type="number" name="data[amount]" id="amount" class="form-control" value="{{ old('data.amount') }}" step="0.01">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="pending" {{ old('status')=='pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ old('status')=='paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="failed" {{ old('status')=='failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="payment_date">Payment Date</label>
                                <input type="text" name="payment_date" id="payment_date" class="form-control flatpickr" value="{{ old('payment_date') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="payer">Payer / Customer</label>
                            <input type="text" name="data[payer]" id="payer" class="form-control" value="{{ old('data.payer') }}">
                        </div>

                        <div class="form-group">
                            <label for="additional_data">Additional Data (JSON or notes)</label>
                            <textarea name="additional_data" id="additional_data" rows="4" class="form-control">{{ old('additional_data') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="data[notes]" id="notes" rows="3" class="form-control">{{ old('data.notes') }}</textarea>
                        </div>

                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Save Transaction</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Meta</h6>
                </div>
                <div class="card-body">
                    <p><strong>Created By</strong></p>
                    <p>{{ Auth::guard('teacher')->user()->name ?? Auth::user()->name ?? '—' }}</p>
                    <hr>
                    <p class="small text-muted">Use the fields to provide transaction details. `data` fields (amount, payer, notes) are stored under the `data` JSON column.</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function(){
        if (typeof flatpickr !== 'undefined') {
            flatpickr('.flatpickr', { dateFormat: 'Y-m-d' });
        }
    });
</script>
@endsection
