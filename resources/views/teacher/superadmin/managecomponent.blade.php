@extends('themes.master')
@section('page_title', 'Kelola Data Sekolah (Super Admin)')
@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Components</h1>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Add Component</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('v1.component.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select name="category" id="category" class="form-control">
                                <option value="" {{ old('category')=='' ? 'selected' : '' }}>-- pilih kategori --</option>
                                <option value="Pendaftaran" {{ old('category')=='Pendaftaran' ? 'selected' : '' }}>Pendaftaran</option>
                                <option value="Pembayaran" {{ old('category')=='Pembayaran' ? 'selected' : '' }}>Pembayaran</option>
                                <option value="Absensi" {{ old('category')=='Absensi' ? 'selected' : '' }}>Absensi</option>
                                <option value="Lainnya" {{ old('category')=='Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="data_raw">Data (format: a=b,c=d)</label>
                            <textarea name="data_raw" id="data_raw" class="form-control" rows="3" placeholder="a=b,c=d"></textarea>
                            <small class="form-text text-muted">Accepted formats: JSON object (e.g. {"a":"b"}), JSON array (e.g. ["a","b"]), key=value pairs (e.g. a=b,c=d). Repeated keys create lists (e.g. a=1,a=2) or provide a comma list (e.g. a,b,c).</small>
                            @error('data_raw')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <button class="btn btn-primary" type="submit">Add Component</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Component List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="componentsTable" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Data</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($components as $component)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $component->name }}</td>
                                        <td>{{ $component->category }}</td>
                                        <td>
                                            @if(is_array($component->data))
                                                @php
                                                    // Detect if associative array (string keys) or list
                                                    $isAssoc = array_keys($component->data) !== range(0, count($component->data) - 1);
                                                @endphp
                                                @if($isAssoc)
                                                    @foreach($component->data as $k => $v)
                                                        <div class="mb-1">
                                                            <strong>{{ $k }}</strong>:
                                                            @if(is_array($v))
                                                                @foreach($v as $item)
                                                                    <span class="badge badge-secondary mr-1">{{ $item }}</span>
                                                                @endforeach
                                                            @else
                                                                <span class="text-muted">{{ $v }}</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    {{-- <div>
                                                        @foreach($component->data as $item)
                                                            <span class="badge badge-secondary mr-1">{{ $item }}</span>
                                                        @endforeach
                                                    </div> --}}
                                                @endif
                                            @else
                                                {{ $component->data }}
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-edit" data-toggle="modal" data-target="#editModal" data-id="{{ $component->id }}" data-name="{{ e($component->name) }}" data-category="{{ e($component->category) }}" data-data='{{ json_encode($component->data) }}' data-dismiss="modal">Edit</button>
                                            <form method="POST" action="{{ route('v1.component.destroy', $component->id) }}" style="display:inline-block;margin-left:6px;" onsubmit="return confirm('Delete this component?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger">Delete</button>
                                            </form>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Component</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form id="editForm" method="POST" action="">
            @csrf
            @method('PUT')
                        <input type="hidden" name="editing_id" id="editing_id" value="">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_name">Name</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_category">Category</label>
                    <select name="category" id="edit_category" class="form-control">
                        <option value="">-- pilih kategori --</option>
                        <option value="Pendaftaran">Pendaftaran</option>
                        <option value="Pembayaran">Pembayaran</option>
                        <option value="Absensi">Absensi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_data_raw">Data (format: a=b,c=d)</label>
                    <textarea name="data_raw" id="edit_data_raw" class="form-control" rows="3" placeholder="a=b,c=d"></textarea>
                    <small class="form-text text-muted">Accepted formats: JSON object (e.g. {"a":"b"}), JSON array (e.g. ["a","b"]), key=value pairs (e.g. a=b,c=d). Repeated keys create lists (e.g. a=1,a=2) or provide a comma list (e.g. a,b,c).</small>
                    @error('data_raw')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                    <div id="edit_data_raw_client_error" class="text-danger small" style="display:none;"></div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    $(function(){
        $('#componentsTable').DataTable();

        $('.btn-edit').on('click', function(){
            var id = $(this).data('id');
            var name = $(this).data('name');
            var category = $(this).data('category');
            var data = $(this).data('data');
            var dataRaw = '';
            if (data && typeof data === 'object'){
                var parts = [];
                // detect assoc vs list
                var isAssoc = false;
                if (!Array.isArray(data)) {
                    isAssoc = true;
                } else {
                    // array with numeric keys is treated as list
                    isAssoc = false;
                }
                if (isAssoc) {
                    for(var k in data){ if (data.hasOwnProperty(k)){
                        var v = data[k];
                        if (Array.isArray(v)) {
                            parts.push(k+'='+v.join(','));
                        } else if (typeof v === 'object') {
                            parts.push(k+'='+JSON.stringify(v));
                        } else {
                            parts.push(k+'='+v);
                        }
                    }}
                } else {
                    for(var i=0;i<data.length;i++){
                        parts.push(data[i]);
                    }
                }
                dataRaw = parts.join(',');
            }
            $('#edit_name').val(name);
            $('#edit_category').val(category);
            $('#edit_data_raw').val(dataRaw);
            $('#editing_id').val(id);
            $('#editForm').attr('action', '/v1/components/'+id);
            $('#editModal').modal('show');
        });

        // Client-side validation for data_raw (add)
        function validateDataRaw(value) {
            value = (value || '').toString().trim();
            if (value === '') return { valid: true };
            // JSON
            if (value[0] === '{' || value[0] === '[') {
                try {
                    var parsed = JSON.parse(value);
                    if (typeof parsed === 'object') return { valid: true };
                    return { valid: false, message: 'JSON must be an object or array.' };
                } catch (e) {
                    return { valid: false, message: 'Invalid JSON: ' + e.message };
                }
            }
            if (value.indexOf('=') !== -1) {
                var pairs = value.split(',').map(function(p){ return p.trim(); }).filter(Boolean);
                for (var i=0;i<pairs.length;i++){
                    var pair = pairs[i];
                    if (pair.indexOf('=') === -1) return { valid: false, message: "Invalid pair '"+pair+"' — expected key=value." };
                    var parts = pair.split('=');
                    var k = parts[0].trim();
                    if (!k) return { valid: false, message: "Empty key in pair '"+pair+"'." };
                    if (!/^[A-Za-z0-9_.-]+$/.test(k)) return { valid: false, message: "Invalid key '"+k+"' — allowed letters, numbers, underscore, dot, hyphen." };
                }
                return { valid: true };
            }
            if (value.indexOf(',') !== -1) {
                var items = value.split(',').map(function(i){ return i.trim(); });
                for (var j=0;j<items.length;j++){
                    if (!items[j]) return { valid: false, message: 'Empty item in comma-separated list.' };
                }
                return { valid: true };
            }
            return { valid: true };
        }

        // Add form client validation
        $('form[action="{{ route('v1.component.store') }}"]').on('submit', function(e){
            var val = $('#data_raw').val();
            var res = validateDataRaw(val);
            if (!res.valid) {
                e.preventDefault();
                $('#data_raw_client_error').text(res.message).show();
                $('html, body').animate({scrollTop: $(this).offset().top - 100}, 200);
            } else {
                $('#data_raw_client_error').hide();
            }
        });

        // Edit form client validation
        $('#editForm').on('submit', function(e){
            var val = $('#edit_data_raw').val();
            var res = validateDataRaw(val);
            if (!res.valid) {
                e.preventDefault();
                $('#edit_data_raw_client_error').text(res.message).show();
                $('#editModal').modal('show');
            } else {
                $('#edit_data_raw_client_error').hide();
            }
        });

        // Hook to clear client error on input
        $('#data_raw').on('input', function(){ $('#data_raw_client_error').hide(); });
        $('#edit_data_raw').on('input', function(){ $('#edit_data_raw_client_error').hide(); });

        // If server-side validation failed for edit, reopen modal and prefill with old input
        @if(old('editing_id'))
            var oldId = '{{ old('editing_id') }}';
            @if($errors->any())
                // Prefill fields from old input
                $(function(){
                    $('#edit_name').val({{ json_encode(old('name', '')) }});
                    $('#edit_category').val({{ json_encode(old('category', '')) }});
                    $('#edit_data_raw').val({{ json_encode(old('data_raw', '')) }});
                    $('#editing_id').val(oldId);
                    $('#editForm').attr('action', '/v1/components/'+oldId);
                    $('#editModal').modal('show');
                });
            @endif
        @endif
    });
</script>

<script>
    function editModal(id) {
    // ... existing code ...

    // Prefill fields from old input
    $(function(){
        $.ajax({
            url: '/v1/components/' + id,
            method: 'GET',
            success: function(data) {
                $('#edit_name').val(data.name);
                $('#edit_category').val(data.category);
                $('#edit_data_raw').val(JSON.stringify(data.data));
                $('#editing_id').val(id);
                $('#editForm').attr('action', '/v1/components/' + id);
                $('#editModal').modal('show');
            }
        });
    });
}
</script>
@endsection
