@extends('themes.master')
@section('page_title', 'Kelola Data Sekolah (Super Admin)')
@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Data Sekolah dan Lainnya</h1>
    </div>

    <!-- SECTION 1: ADD MANDATORY COMPONENT FORM -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3 bg-success">
                    <h6 class="m-0 font-weight-bold text-white">Add Mandatory Component</h6>
                </div>
                <div class="card-body">
                    @if(session('success_mandatory'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success_mandatory') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('v1.component.store') }}" id="addMandatoryComponentForm" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group">
                            <label for="mandatory_name" class="font-weight-bold">Component Name *</label>
                            <select 
                                name="name" 
                                id="mandatory_name" 
                                class="form-control @error('name') is-invalid @enderror"
                                required>
                                <option value="">-- Select Component --</option>
                                <option value="School Name">School Name</option>
                                <option value="School Logo">School Logo</option>
                                <option value="Class">Class</option>
                                <option value="Surat Peringatan 1">Surat Peringatan 1</option>
                                <option value="Surat Peringatan 2">Surat Peringatan 2</option>
                                <option value="Surat Pemanggilan Orang Tua">Surat Pemanggilan Orang Tua</option>
                                <option value="Tahun Ajaran">Tahun Ajaran</option>
                            </select>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <input type="hidden" name="category" value="mandatory">

                        <div class="form-group">
                            <label for="mandatory_data_raw" class="font-weight-bold">Data</label>
                            <textarea 
                                name="data_raw" 
                                id="mandatory_data_raw" 
                                class="form-control @error('data_raw') is-invalid @enderror" 
                                rows="4" 
                                placeholder='Supported formats:&#10;JSON: {"key":"value"}&#10;List: item1,item2,item3&#10;Key-Value: key1=value1,key2=value2'></textarea>
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

                        <div class="form-group" id="mandatory_upload_group" style="display:none;">
                            <label for="mandatory_upload_file" class="font-weight-bold">Upload File</label>
                            <input 
                                type="file" 
                                name="upload_file" 
                                id="mandatory_upload_file" 
                                class="form-control-file"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="form-text text-muted">Accepted: PDF, DOC, DOCX, JPG, PNG (Max 5MB)</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add Mandatory Component
                            </button>
                            <button type="reset" class="btn btn-secondary ml-2">Clear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: MANDATORY COMPONENTS DATATABLE -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3 bg-success">
                    <h6 class="m-0 font-weight-bold text-white">Mandatory Components</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="mandatoryComponentsTable" width="100%">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Name</th>
                                    <th width="40%">Data</th>
                                    <th width="15%">File</th>
                                    <th width="20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mandatoryComponents as $component)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $component->name }}</strong>
                                        </td>
                                        <td>
                                            <code class="text-break">
                                                @if(is_array($component->data) || is_object($component->data))
                                                    @php
                                                        $displayData = $component->data;
                                                        if (is_array($displayData) && isset($displayData['uploads'])) {
                                                            unset($displayData['uploads']);
                                                        }
                                                    @endphp
                                                    {{ json_encode($displayData) }}
                                                @else
                                                    {{ $component->data ?? 'N/A' }}
                                                @endif
                                            </code>
                                        </td>
                                        <td>
                                            @php
                                                $uploadPath = null;
                                                if (is_array($component->data) && isset($component->data['uploads'])) {
                                                    $uploadPath = $component->data['uploads'];
                                                }
                                            @endphp
                                            @if($uploadPath)
                                                <a href="{{ asset($uploadPath) }}" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-download"></i> View
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info btn-edit-mandatory" 
                                                data-id="{{ $component->id }}"
                                                data-name="{{ $component->name }}"
                                                data-data="{{ json_encode($component->data) }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form 
                                                method="POST" 
                                                action="{{ route('v1.component.destroy', $component->id) }}" 
                                                style="display:inline;" 
                                                onsubmit="return confirm('Are you sure you want to delete this mandatory component?');">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete Component">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox"></i> No mandatory components found.
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

    <!-- SECTION 3: ADD OTHER COMPONENT FORM -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">Add Other Component</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

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

                    <form method="POST" action="{{ route('v1.component.store') }}" id="addComponentForm">
                        @csrf
                        
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Component Name *</label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name" 
                                class="form-control @error('name') is-invalid @enderror" 
                                placeholder="Enter component name"
                                value="{{ old('name') }}"
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
                                <option value="Pendaftaran" {{ old('category') == 'Pendaftaran' ? 'selected' : '' }}>Pendaftaran</option>
                                <option value="Pembayaran" {{ old('category') == 'Pembayaran' ? 'selected' : '' }}>Pembayaran</option>
                                <option value="Absensi" {{ old('category') == 'Absensi' ? 'selected' : '' }}>Absensi</option>
                                <option value="Lainnya" {{ old('category') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                                rows="4" 
                                placeholder='Supported formats:&#10;JSON: {"key":"value"}&#10;List: item1,item2,item3&#10;Key-Value: key1=value1,key2=value2'
                                value="{{ old('data_raw') }}"></textarea>
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
                                <i class="fas fa-plus"></i> Add Component
                            </button>
                            <button type="reset" class="btn btn-secondary ml-2">Clear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 4: OTHER COMPONENT LIST DATATABLE -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">Other Components List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="componentsTable" width="100%">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Name</th>
                                    <th width="15%">Category</th>
                                    <th width="40%">Data</th>
                                    <th width="20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($components as $component)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $component->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $component->category ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <code class="text-break">
                                                @if(is_array($component->data) || is_object($component->data))
                                                    {{ json_encode($component->data) }}
                                                @else
                                                    {{ $component->data ?? 'N/A' }}
                                                @endif
                                            </code>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('v1.component.edit', $component->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form 
                                                method="POST" 
                                                action="{{ route('v1.component.destroy', $component->id) }}" 
                                                style="display:inline;" 
                                                onsubmit="return confirm('Are you sure you want to delete this component?');">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete Component">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox"></i> No components found. Add one to get started.
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

</div>

<!-- EDIT COMPONENT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editModalLabel">Edit Component</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    
                    <div class="form-group">
                        <label for="edit_name" class="font-weight-bold">Component Name *</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="edit_name" 
                            class="form-control"
                            placeholder="Enter component name"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="edit_category" class="font-weight-bold">Category *</label>
                        <select name="category" id="edit_category" class="form-control">
                            <option value="">-- Select Category --</option>
                            <option value="Pendaftaran">Pendaftaran</option>
                            <option value="Pembayaran">Pembayaran</option>
                            <option value="Absensi">Absensi</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_data_raw" class="font-weight-bold">Data</label>
                        <textarea 
                            name="data_raw" 
                            id="edit_data_raw" 
                            class="form-control" 
                            rows="4"
                            placeholder='Supported formats:&#10;JSON: {"key":"value"}&#10;List: item1,item2,item3&#10;Key-Value: key1=value1,key2=value2'></textarea>
                        <small class="form-text text-muted d-block mt-2">
                            <strong>Supported Formats:</strong><br>
                            • JSON Object: <code>{"name":"John"}</code><br>
                            • JSON Array: <code>["item1","item2"]</code><br>
                            • Key-Value: <code>key1=value1,key2=value2</code><br>
                            • List: <code>item1,item2,item3</code>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function(){
    // Initialize DataTables
    $('#mandatoryComponentsTable').DataTable({
        "order": [[0, "desc"]],
        "language": {
            "emptyTable": "No mandatory components found"
        }
    });
    
    var table = $('#componentsTable').DataTable({
        "order": [[0, "desc"]],
        "language": {
            "emptyTable": "No components found"
        }
    });

    // Show/hide upload field based on selected mandatory component
    $('#mandatory_name').on('change', function() {
        var selectedName = $(this).val();
        var uploadRequiredNames = ['School Logo', 'Surat Peringatan 1', 'Surat Peringatan 2', 'Surat Pemanggilan Orang Tua'];
        
        if (uploadRequiredNames.includes(selectedName)) {
            $('#mandatory_upload_group').show();
        } else {
            $('#mandatory_upload_group').hide();
            $('#mandatory_upload_file').val('');
        }

        // Check if component already exists and auto-fill
        fetchExistingMandatoryComponent(selectedName);
    });

    // Function to fetch existing mandatory component data
    function fetchExistingMandatoryComponent(name) {
        if (!name) return;
        
        $.ajax({
            url: '/v1/components/mandatory/' + encodeURIComponent(name),
            method: 'GET',
            success: function(response) {
                if (response.success && response.component) {
                    var data = response.component.data;
                    var dataDisplay = '';
                    
                    // Remove uploads key for display
                    if (data && typeof data === 'object' && data.uploads) {
                        var displayData = Object.assign({}, data);
                        delete displayData.uploads;
                        dataDisplay = JSON.stringify(displayData);
                    } else if (data) {
                        if (Array.isArray(data) || typeof data === 'object') {
                            dataDisplay = JSON.stringify(data);
                        } else {
                            dataDisplay = data;
                        }
                    }
                    
                    $('#mandatory_data_raw').val(dataDisplay);
                } else {
                    $('#mandatory_data_raw').val('');
                }
            },
            error: function() {
                $('#mandatory_data_raw').val('');
            }
        });
    }

    // Handle Edit Mandatory Component Button Click
    $(document).on('click', '.btn-edit-mandatory', function(){
        var id = $(this).data('id');
        var name = $(this).data('name');
        var rawData = $(this).attr('data-data');
        
        // Set form fields
        $('#mandatory_name').val(name).trigger('change');
        
        // Parse and display data
        try {
            var data = rawData ? JSON.parse(rawData) : null;
            var dataDisplay = '';
            
            if (data && typeof data === 'object') {
                var displayData = Object.assign({}, data);
                delete displayData.uploads; // Remove uploads for display
                
                dataDisplay = JSON.stringify(displayData);
            }
            
            $('#mandatory_data_raw').val(dataDisplay);
        } catch(e) {
            console.error('Error parsing data:', e);
        }
        
        // Scroll to form
        $('html, body').animate({
            scrollTop: $('#addMandatoryComponentForm').offset().top - 100
        }, 500);
    });

    // Handle Edit Button Click for Other Components
    $(document).on('click', '.btn-edit', function(){
        var id = $(this).data('id');
        var name = $(this).data('name');
        var category = $(this).data('category');
        
        // Parse data attribute
        var rawData = $(this).attr('data-data');
        var data = null;
        var dataDisplay = '';
        
        try {
            data = rawData ? JSON.parse(rawData) : null;
            
            // Convert data back to string format for textarea
            if (data && typeof data === 'object') {
                if (Array.isArray(data)) {
                    // If array, convert to comma-separated
                    dataDisplay = data.join(',');
                } else {
                    // If object, convert to JSON string
                    dataDisplay = JSON.stringify(data);
                }
            }
        } catch(e) {
            console.error('Error parsing data:', e);
            dataDisplay = rawData || '';
        }
        
        // Populate modal fields
        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_category').val(category);
        $('#edit_data_raw').val(dataDisplay);
        
        // Set form action
        $('#editForm').attr('action', '/v1/components/' + id);
        
        // Show modal
        $('#editModal').modal('show');
    });

    // Handle Edit Form Submission
    $('#editForm').on('submit', function(e){
        e.preventDefault();
        
        var id = $('#edit_id').val();
        var name = $('#edit_name').val();
        var category = $('#edit_category').val();
        var dataRaw = $('#edit_data_raw').val();
        
        // Validate required fields
        if (!name.trim()) {
            alert('Component name is required');
            return false;
        }
        
        if (!category) {
            alert('Category is required');
            return false;
        }
        
        // Submit the form
        this.submit();
    });
});
</script>
@endsection
