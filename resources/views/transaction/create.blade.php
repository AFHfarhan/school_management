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
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Transaction Details</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('v1.transaction.store') }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="name">Transaction Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('title', request('title') ?? $transactionTitle ?? '') }}" disabled>
                                <input type="hidden" name="title" class="form-control" value="{{ old('title', request('title') ?? $transactionTitle ?? '') }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="category">Transaction Type</label>
                                @php $selectedType = $preselectedType ?? old('category') ?? null; @endphp
                                <select name="{{ $selectedType ? 'category_disabled' : 'category' }}" id="category" class="form-control" {{ $selectedType ? 'disabled' : '' }}>
                                    @php
                                        $types = $types ?? ['spp' => 'SPP', 'ppdb' => 'PPDB', 'other' => 'Other'];
                                    @endphp
                                    @foreach($types as $k=>$v)
                                        <option value="{{ $k }}" {{ ($selectedType == $k) ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                                @if($selectedType)
                                    <input type="hidden" name="category" value="{{ $selectedType }}">
                                @else
                                    <input type="hidden" name="category" value="{{ old('category') }}">
                                @endif
                            </div>
                        </div>

                        <div class="form-row">
                            <label for="amount">Amount</label>
                            <input type="text" name="data[amount]" id="amount" class="form-control" value="{{ old('data.amount') }}" >
                        </div>
                        <div class="form-row">
                            <label for="amount_terbilang">Amount in Words (Terbilang)</label>
                            <input type="text" name="data[amount_terbilang]" id="amount_terbilang" class="form-control" value="{{ old('data.amount_terbilang') }}">
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="payer">Payer / Customer</label>
                                <input type="text" name="data[payer]" id="payer" class="form-control" value="{{ old('data.payer') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="recipient">Recipient</label>
                                <input type="text" name="data[recipient]" id="recipient" class="form-control" value="{{ old('data.recipient', Auth::guard('teacher')->user()->name ?? Auth::user()->name ?? '') }}">
                            </div>
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
    </div>

</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ensure flatpickr is loaded and initialize
        if (typeof flatpickr !== 'undefined') {
            flatpickr(".flatpickr", { 
                dateFormat: "d/m/Y",
                allowInput: true,
                altInput: true,
                altFormat: "d/m/Y"
            });
        } else {
            console.error('Flatpickr library not loaded');
        }
    });

    document.getElementById('amount').addEventListener('input', function (e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        e.target.value = new Intl.NumberFormat('id-ID').format(value);
    });
</script>
@endsection

