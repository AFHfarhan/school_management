@extends('themes.master')
@section('page_title', 'Update Payment Status')
@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Update Payment Status</h1>
        <a href="{{ route('v1.transaction.index') }}" class="btn btn-secondary">
            <i class="fas fa-list"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Information</h6>
                </div>
                <div class="card-body">
                    <!-- Transaction Info -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold">Transaction Details:</h6>
                        <p class="mb-1"><strong>ID:</strong> {{ 'TR/SMK/'. strtoupper($transaction->category) . "/" . $transaction->id . '/' . $transaction->created_at->format('d/m/y') }}</p>
                        <p class="mb-1"><strong>Name:</strong> {{ $transaction->name }}</p>
                        <p class="mb-1"><strong>Category:</strong> {{ strtoupper($transaction->category) }}</p>
                        @php
                            $data = is_array($transaction->data) ? $transaction->data : json_decode($transaction->data, true);
                        @endphp
                        @if(isset($data['amount']))
                            <p class="mb-1"><strong>Amount:</strong> Rp {{ $data['amount'] }}</p>
                        @endif
                    </div>

                    <hr>

                    <!-- Payment Form -->
                    <form method="POST" action="{{ route('v1.transaction.update', $transaction->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="payment_date">Payment Date <span class="text-danger">*</span></label>
                            <input type="text" name="data[payment_date]" id="payment_date" class="form-control flatpickr" value="{{ old('data.payment_date', now()->format('d/m/Y')) }}" readonly>
                            <small class="form-text text-muted">Auto-filled with current date</small>
                            @error('data.payment_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="payment_evidence">Payment Evidence Images <span class="text-danger">*</span></label>
                            <input type="file" name="payment_evidence[]" id="payment_evidence" class="form-control-file" accept="image/*" multiple required>
                            <small class="form-text text-muted">Upload one or multiple payment evidence images (jpeg, png, jpg, gif). Max size per image: 2MB</small>
                            @error('payment_evidence')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            @error('payment_evidence.*')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Image Preview Section -->
                        <div class="form-group">
                            <label>Image Preview</label>
                            <div id="image_preview_container" class="mt-2" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                                <!-- Previews will be added here -->
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Upon submission, the payment status will be automatically updated to <strong>PAID</strong> and payment evidence will be stored.
                        </div>

                        <div class="form-group d-flex justify-content-between">
                            <a href="{{ route('v1.transaction.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Submit Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize flatpickr (readonly, no datepicker)
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#payment_date", { 
                dateFormat: "d/m/Y",
                allowInput: false
            });
        }

        // Multiple image preview
        document.getElementById('payment_evidence').addEventListener('change', function(e) {
            const files = e.target.files;
            const previewContainer = document.getElementById('image_preview_container');
            previewContainer.innerHTML = ''; // Clear previous previews

            if (files.length === 0) {
                previewContainer.style.display = 'none';
                return;
            }

            previewContainer.style.display = 'grid';

            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'position-relative';
                        previewDiv.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${index + 1}" class="img-thumbnail" style="width: 100%; height: 200px; object-fit: cover;">
                            <small class="d-block mt-2 text-center">${file.name}</small>
                        `;
                        previewContainer.appendChild(previewDiv);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    });
</script>
@endsection
