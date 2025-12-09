@extends('themes.master')
@section('page_title', 'Edit Data - Super Admin')
@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Data</h1>
        <a href="{{ route('v1.component.manage') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Validation Error:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Edit Component Section -->
    @if(isset($component) && $component)
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card shadow">
                    <div class="card-header py-3 bg-primary">
                        <h6 class="m-0 font-weight-bold text-white">Edit Component</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('v1.component.update', $component->id) }}" id="editComponentForm">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="name" class="font-weight-bold">Component Name *</label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="name" 
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Enter component name"
                                    value="{{ old('name', $component->name) }}"
                                    required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="category" class="font-weight-bold">Category *</label>
                                <select 
                                    name="category" 
                                    id="category" 
                                    class="form-control @error('category') is-invalid @enderror">
                                    <option value="">-- Select Category --</option>
                                    <option value="Pendaftaran" {{ old('category', $component->category) == 'Pendaftaran' ? 'selected' : '' }}>Pendaftaran</option>
                                    <option value="Pembayaran" {{ old('category', $component->category) == 'Pembayaran' ? 'selected' : '' }}>Pembayaran</option>
                                    <option value="Absensi" {{ old('category', $component->category) == 'Absensi' ? 'selected' : '' }}>Absensi</option>
                                    <option value="Lainnya" {{ old('category', $component->category) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('category')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="data_raw" class="font-weight-bold">Data</label>
                                <textarea 
                                    name="data_raw" 
                                    id="data_raw" 
                                    class="form-control @error('data_raw') is-invalid @enderror"
                                    rows="5"
                                    placeholder='Supported formats:&#10;JSON: {"key":"value"}&#10;List: item1,item2,item3&#10;Key-Value: key1=value1,key2=value2'>@php
$dataDisplay = '';
if ($component && $component->data) {
    if (is_array($component->data)) {
        if (array_keys($component->data) === range(0, count($component->data) - 1)) {
            $dataDisplay = implode(',', $component->data);
        } else {
            $dataDisplay = json_encode($component->data);
        }
    } else {
        $dataDisplay = (string) $component->data;
    }
}
echo old('data_raw', $dataDisplay);
@endphp</textarea>
                                <small class="form-text text-muted d-block mt-2">
                                    <strong>Supported Formats:</strong><br>
                                    • JSON Object: <code>{"name":"John","age":"30"}</code><br>
                                    • JSON Array: <code>["item1","item2","item3"]</code><br>
                                    • Key-Value Pairs: <code>key1=value1,key2=value2</code><br>
                                    • Comma-Separated List: <code>item1,item2,item3</code>
                                </small>
                                @error('data_raw')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <a href="{{ route('v1.component.manage') }}" class="btn btn-secondary ml-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Teacher Section -->
    @if(isset($teacher) && $teacher)
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card shadow">
                    <div class="card-header py-3 bg-success">
                        <h6 class="m-0 font-weight-bold text-white">Edit Teacher</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('v1.teacher.update', $teacher->id) }}" id="editTeacherForm">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="teacher_name" class="font-weight-bold">Teacher Name *</label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="teacher_name" 
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Enter teacher name"
                                    value="{{ old('name', $teacher->name) }}"
                                    required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="teacher_email" class="font-weight-bold">Email *</label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    id="teacher_email" 
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="Enter teacher email"
                                    value="{{ old('email', $teacher->email) }}"
                                    required>
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="teacher_role" class="font-weight-bold">Role *</label>
                                <select 
                                    name="role" 
                                    id="teacher_role" 
                                    class="form-control @error('role') is-invalid @enderror">
                                    <option value="">-- Select Role --</option>
                                    <option value="guru" {{ old('role', $teacher->data['role'] ?? null) == 'guru' ? 'selected' : '' }}>Guru</option>
                                    <option value="tata_usaha" {{ old('role', $teacher->data['role'] ?? null) == 'tata_usaha' ? 'selected' : '' }}>Tata Usaha</option>
                                    <option value="ka_tata_usaha" {{ old('role', $teacher->data['role'] ?? null) == 'ka_tata_usaha' ? 'selected' : '' }}>Kepala Tata Usaha</option>
                                    <option value="kepala_sekolah" {{ old('role', $teacher->data['role'] ?? null) == 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                    <option value="kesiswaan" {{ old('role', $teacher->data['role'] ?? null) == 'kesiswaan' ? 'selected' : '' }}>Kesiswaan</option>
                                </select>
                                @error('role')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="teacher_password" class="font-weight-bold">Password (Leave blank to keep current)</label>
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="teacher_password" 
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Enter new password if you want to change it">
                                <small class="form-text text-muted">Minimum 6 characters</small>
                                @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <a href="{{ route('v1.teacher.manage') }}" class="btn btn-secondary ml-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
