@extends('themes.master')
@section('page_title', 'Kelola Jadwal Guru')

@section('content')
<div class="container-fluid">
		<div class="d-sm-flex align-items-center justify-content-between mb-4">
				<h1 class="h3 mb-0 text-gray-800">Kelola Jadwal Guru</h1>
				<a href="{{ route('v1.teacher.schedule.manage') }}" class="btn btn-secondary btn-sm"><i class="fas fa-redo"></i> Reset</a>
		</div>

		@if(session('success'))
				<div class="alert alert-success">{{ session('success') }}</div>
		@endif
		@if(session('error'))
				<div class="alert alert-danger">{{ session('error') }}</div>
		@endif

		<div class="row">
				<div class="col-lg-12">
						<div class="card shadow mb-4">
								<div class="card-header py-3">
										<h6 class="m-0 font-weight-bold text-primary">Form Jadwal</h6>
								</div>
								<div class="card-body">
										<form method="POST" action="{{ route('v1.teacher.schedule.upsert') }}" id="scheduleForm">
												@csrf
												<input type="hidden" name="id" value="{{ $editing->id ?? '' }}">

											<div class="form-group">
												<label>Guru</label>
												<select class="form-control" name="guru" required>
													<option value="">-- Pilih Guru --</option>
													@php $guruVal = old('guru', $editing->guru ?? ''); @endphp
													@foreach(($teachers ?? []) as $t)
														<option value="{{ $t['name'] }}" {{ $guruVal === $t['name'] ? 'selected' : '' }}>{{ $t['name'] }}</option>
													@endforeach
												</select>
											</div>

												<div class="form-group">
														<label>Tahun Ajaran</label>
														<input type="text" class="form-control" name="tahun_ajaran" value="{{ old('tahun_ajaran', $editing->tahun_ajaran ?? $tahunAjaranName) }}" readonly>
												</div>

												<div class="form-group">
														<label>Semester</label>
														<select class="form-control" name="semester" required>
																@php $semVal = old('semester', $editing->semester ?? 'Semester Ganjil'); @endphp
																<option value="Semester Ganjil" {{ $semVal==='Semester Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
																<option value="Semester Genap" {{ $semVal==='Semester Genap' ? 'selected' : '' }}>Semester Genap</option>
														</select>
												</div>

												<div class="form-row">
														<div class="form-group col-md-6">
																<label>Start Date</label>
																<input type="text" class="form-control datepicker" name="startDate" placeholder="YYYY-MM-DD" value="{{ old('startDate', $editing->startDate ?? $defaultStartDate) }}" required>
														</div>
														<div class="form-group col-md-6">
																<label>End Date</label>
																<input type="text" class="form-control datepicker" name="endDate" placeholder="YYYY-MM-DD" value="{{ old('endDate', $editing->endDate ?? $defaultEndDate) }}" required>
														</div>
												</div>

												<hr>
												<h6 class="mb-2">Rangkaian Jadwal</h6>

												<div id="scheduleList"></div>
												<button type="button" class="btn btn-sm btn-outline-primary mb-3" id="addScheduleBtn"><i class="fas fa-plus"></i> Tambah Baris Jadwal</button>

												<input type="hidden" name="data" id="dataField">

												<div class="text-right">
														<button type="submit" class="btn btn-primary">Simpan / Perbarui</button>
												</div>
										</form>
								</div>
						</div>
				</div>
		</div>

		<div class="row">
				<div class="col-lg-12">
						<div class="card shadow mb-4">
								<div class="card-header py-3">
										<h6 class="m-0 font-weight-bold text-primary">Daftar Jadwal Tersimpan</h6>
								</div>
								<div class="card-body">
										<div class="table-responsive">
												<table class="table table-bordered table-hover">
														<thead class="bg-light">
																<tr>
																		<th width="5%">#</th>
																		<th>Guru</th>
																		<th>Tahun Ajaran</th>
																		<th>Semester</th>
																		<th>Rentang</th>
																		<th width="15%">Aksi</th>
																</tr>
														</thead>
														<tbody>
																@forelse($schedules as $idx => $s)
																		<tr>
																				<td>{{ $idx + 1 }}</td>
																				<td>{{ $s->guru ?? '-' }}</td>
																				<td>{{ $s->tahun_ajaran }}</td>
																				<td>{{ $s->semester }}</td>
																				<td>{{ $s->startDate }} - {{ $s->endDate }}</td>
																				<td>
																						<a href="{{ route('v1.teacher.schedule.manage', ['id' => $s->id]) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i> Edit</a>
																				</td>
																		</tr>
																@empty
																		<tr>
																				<td colspan="5" class="text-center text-muted">Belum ada data</td>
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
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('global_assets/vendor/flatpickr/flatpickr.min.css') }}">
<style>
	.mapel-item { border-left: 3px solid #e3e6f0; padding-left: 10px; }
	.small-help { font-size: 12px; color: #6c757d; }
	.schedule-box { border: 1px solid #e3e6f0; border-radius: .35rem; padding: 12px; margin-bottom: 10px; }
	.remove-btn { cursor: pointer; }
	.form-row.g-2 > [class^="col-"] { padding-right: 8px; padding-left: 8px; }
	.mapel-row { margin-bottom: 8px; }
	.mapel-row .form-control { height: calc(1.5em + .75rem + 2px); }
	.btn-xxs { padding: .15rem .35rem; font-size: .75rem; }
	.json-preview { white-space: pre-wrap; background: #f8f9fc; border: 1px dashed #d1d3e2; padding: 8px; border-radius: 6px; }
	.json-preview[hidden] { display: none; }
	.toggle-preview { font-size: 12px; }
	.w-120 { width: 120px; }
	.w-200 { width: 200px; }
	.w-180 { width: 180px; }
	.w-150 { width: 150px; }
	.w-90 { width: 90px; }
	.w-60 { width: 60px; }
	.chip { display: inline-block; padding: 2px 8px; border-radius: 10px; background: #f1f3f5; margin-right: 6px; font-size: 12px; }
	.chip.info { background: #e8f4ff; color: #0b6bcb; }
	.chip.warn { background: #fff4e6; color: #b35c00; }
	.chip.success { background: #e6fcf5; color: #0c6b58; }
	.chip.muted { background: #f8f9fa; color: #6c757d; }
</style>
@endpush

@push('scripts')
<script src="{{ asset('global_assets/vendor/flatpickr/flatpickr.min.js') }}"></script>
<script>
// Debug: Check if script is loading
console.log('=== INLINE SCRIPT LOADED ===');
console.log('Document ready state:', document.readyState);
console.log('jQuery loaded:', typeof jQuery !== 'undefined');

// Use vanilla JavaScript with multiple fallback methods
(function() {
	'use strict';
	
	console.log('=== Schedule Manager Script Starting ===');
	
	// Multiple initialization attempts
	let initAttempts = 0;
	const maxAttempts = 10;
	
	function tryInit() {
		initAttempts++;
		console.log(`Init attempt #${initAttempts}`);
		
		const scheduleList = document.getElementById('scheduleList');
		const addScheduleBtn = document.getElementById('addScheduleBtn');
		const dataField = document.getElementById('dataField');
		const scheduleForm = document.getElementById('scheduleForm');
		
		console.log('Elements check:', {
			scheduleList: scheduleList ? 'FOUND' : 'NOT FOUND',
			addScheduleBtn: addScheduleBtn ? 'FOUND' : 'NOT FOUND',
			dataField: dataField ? 'FOUND' : 'NOT FOUND',
			scheduleForm: scheduleForm ? 'FOUND' : 'NOT FOUND'
		});
		
		if (!scheduleList || !addScheduleBtn || !dataField || !scheduleForm) {
			if (initAttempts < maxAttempts) {
				console.warn(`Elements not ready, retrying in 100ms...`);
				setTimeout(tryInit, 100);
			} else {
				console.error('FAILED: Could not find required elements after', maxAttempts, 'attempts');
			}
			return;
		}
		
		console.log('✓ All elements found, initializing...');

		// Incoming data for editing
		const editingData = @json(isset($editing) && $editing && is_array($editing->data) ? $editing->data : []);
		let schedule = (editingData && editingData.schedule) ? editingData.schedule : [];
		
		// Classes from component
		const classesData = @json($classes ?? []);
		console.log('Classes data:', classesData);
		console.log('Classes type:', typeof classesData);
		console.log('Classes is array:', Array.isArray(classesData));
		
		// Build kelas options HTML - handle both array of strings and array of objects
		const kelasOptions = (Array.isArray(classesData) ? classesData : []).map(kelas => {
			// Handle if kelas is a string directly
			let kelasValue = '';
			if (typeof kelas === 'string') {
				kelasValue = kelas;
			} else if (typeof kelas === 'object' && kelas !== null) {
				// Handle if kelas is an object with name/class/value properties
				kelasValue = kelas.name || kelas.class || kelas.value || kelas.nama || '';
			}
			return kelasValue ? `<option value="${kelasValue}">${kelasValue.toUpperCase()}</option>` : '';
		}).filter(opt => opt !== '').join('');
		
		console.log('Kelas options HTML:', kelasOptions);
		console.log('Initial schedule data:', schedule);
		console.log('Editing data:', editingData);

		function render() {
			console.log('Rendering schedule:', schedule);
			scheduleList.innerHTML = '';
			schedule.forEach((row, idx) => {
			const box = document.createElement('div');
			box.className = 'schedule-box';
			
			// Build kelas options with selected state
			const kelasOptionsWithSelected = (Array.isArray(classesData) ? classesData : []).map(kelas => {
				// Handle if kelas is a string directly
				let kelasValue = '';
				if (typeof kelas === 'string') {
					kelasValue = kelas;
				} else if (typeof kelas === 'object' && kelas !== null) {
					// Handle if kelas is an object with name/class/value properties
					kelasValue = kelas.name || kelas.class || kelas.value || kelas.nama || '';
				}
				const selected = row.kelas === kelasValue ? 'selected' : '';
				return kelasValue ? `<option value="${kelasValue}" ${selected}>${kelasValue.toUpperCase()}</option>` : '';
			}).filter(opt => opt !== '').join('');
			
			box.innerHTML = `
					<div class="d-flex justify-content-between align-items-center mb-2">
						<div>
							<span class="chip info">Hari</span>
							<span class="chip warn">Kelas</span>
							<span class="chip success">Mapel</span>
						</div>
						<button type="button" class="btn btn-outline-danger btn-xxs remove-btn" data-idx="${idx}"><i class="fas fa-trash"></i> Hapus</button>
					</div>
					<div class="form-row">
						<div class="form-group col-md-4">
							<label>Hari</label>
						<select class="form-control" data-field="hari" data-idx="${idx}">
							<option value="">-- Pilih Hari --</option>
							<option value="senin" ${row.hari === 'senin' ? 'selected' : ''}>Senin</option>
							<option value="selasa" ${row.hari === 'selasa' ? 'selected' : ''}>Selasa</option>
							<option value="rabu" ${row.hari === 'rabu' ? 'selected' : ''}>Rabu</option>
							<option value="kamis" ${row.hari === 'kamis' ? 'selected' : ''}>Kamis</option>
							<option value="jumat" ${row.hari === 'jumat' ? 'selected' : ''}>Jumat</option>
							<option value="sabtu" ${row.hari === 'sabtu' ? 'selected' : ''}>Sabtu</option>
							<option value="minggu" ${row.hari === 'minggu' ? 'selected' : ''}>Minggu</option>
						</select>
						</div>
						<div class="form-group col-md-8">
							<label>Kelas</label>
						<select class="form-control" data-field="kelas" data-idx="${idx}">
							<option value="">-- Pilih Kelas --</option>
							${kelasOptionsWithSelected}
						</select>
						</div>
					</div>
					<div class="mb-2"><strong>Mata Pelajaran</strong></div>
					<div class="mapel-list" id="mapel-${idx}">
						${(row.mapel || []).map((m, mIdx) => mapelRow(idx, mIdx, m)).join('')}
					</div>
					<button type="button" class="btn btn-sm btn-outline-secondary" data-action="add-mapel" data-idx="${idx}"><i class="fas fa-plus"></i> Tambah Mapel</button>
				`;
				scheduleList.appendChild(box);
			});

			// update hidden field
			const jsonData = JSON.stringify({ schedule });
			dataField.value = jsonData;
			console.log('Updated dataField:', jsonData);
		}

		function mapelRow(sIdx, mIdx, m) {
			return `
				<div class="form-row mapel-row" data-sidx="${sIdx}" data-midx="${mIdx}">
					<div class="col-md-3">
						<select class="form-control" data-mapel="waktu" data-sidx="${sIdx}" data-midx="${mIdx}">
							<option value="">-- Pilih Waktu --</option>
							<option value="08:00 - 09:00" ${m.waktu === '08:00 - 09:00' ? 'selected' : ''}>08:00 - 09:00</option>
							<option value="09:00 - 10:00" ${m.waktu === '09:00 - 10:00' ? 'selected' : ''}>09:00 - 10:00</option>
							<option value="10:00 - 11:00" ${m.waktu === '10:00 - 11:00' ? 'selected' : ''}>10:00 - 11:00</option>
							<option value="11:00 - 12:00" ${m.waktu === '11:00 - 12:00' ? 'selected' : ''}>11:00 - 12:00</option>
							<option value="13:00 - 14:00" ${m.waktu === '13:00 - 14:00' ? 'selected' : ''}>13:00 - 14:00</option>
							<option value="14:00 - 15:00" ${m.waktu === '14:00 - 15:00' ? 'selected' : ''}>14:00 - 15:00</option>
							<option value="15:00 - 16:00" ${m.waktu === '15:00 - 16:00' ? 'selected' : ''}>15:00 - 16:00</option>
							<option value="16:00 - 17:00" ${m.waktu === '16:00 - 17:00' ? 'selected' : ''}>16:00 - 17:00</option>
						</select>
					</div>
					<div class="col-md-7">
						<select class="form-control" data-mapel="nama" data-sidx="${sIdx}" data-midx="${mIdx}">
							<option value="">-- Pilih Mata Pelajaran --</option>
							<option value="Praktikum TKJ" ${m.nama === 'Praktikum TKJ' ? 'selected' : ''}>Praktikum TKJ</option>
							<option value="Pengkajian RPL" ${m.nama === 'Pengkajian RPL' ? 'selected' : ''}>Pengkajian RPL</option>
							<option value="Pelatihan Multimedia" ${m.nama === 'Pelatihan Multimedia' ? 'selected' : ''}>Pelatihan Multimedia</option>
							<option value="Pembelajaran IPA" ${m.nama === 'Pembelajaran IPA' ? 'selected' : ''}>Pembelajaran IPA</option>
							<option value="Olahraga" ${m.nama === 'Olahraga' ? 'selected' : ''}>Olahraga</option>
							<option value="Pengayaan BK" ${m.nama === 'Pengayaan BK' ? 'selected' : ''}>Pengayaan BK</option>
						</select>
					</div>
					<div class="col-md-2">
						<button type="button" class="btn btn-outline-danger btn-xxs" data-action="remove-mapel" data-sidx="${sIdx}" data-midx="${mIdx}"><i class="fas fa-times"></i></button>
					</div>
				</div>
			`;
		}

		// Handle both input and change events for text inputs and dropdowns
		scheduleList.addEventListener('input', handleFieldUpdate);
		scheduleList.addEventListener('change', handleFieldUpdate);
		
		function handleFieldUpdate(e) {
			const t = e.target;
			if (t.hasAttribute('data-field')) {
				const idx = parseInt(t.getAttribute('data-idx'));
				const field = t.getAttribute('data-field');
				schedule[idx][field] = t.value;
				console.log(`Updated schedule[${idx}].${field}:`, t.value);
				dataField.value = JSON.stringify({ schedule });
			} else if (t.hasAttribute('data-mapel')) {
				const sIdx = parseInt(t.getAttribute('data-sidx'));
				const mIdx = parseInt(t.getAttribute('data-midx'));
				const field = t.getAttribute('data-mapel');
				schedule[sIdx].mapel[mIdx][field] = t.value;
				console.log(`Updated schedule[${sIdx}].mapel[${mIdx}].${field}:`, t.value);
				dataField.value = JSON.stringify({ schedule });
			}
		}

		scheduleList.addEventListener('click', (e) => {
			const t = e.target.closest('button');
			if (!t) return;
			const action = t.getAttribute('data-action');
			
			console.log('Button clicked:', { action, classList: t.classList.toString() });
			
			if (t.classList.contains('remove-btn')) {
				const idx = parseInt(t.getAttribute('data-idx'));
				console.log('Removing schedule row:', idx);
				schedule.splice(idx, 1);
				render();
				return;
			}
			if (action === 'add-mapel') {
				const idx = parseInt(t.getAttribute('data-idx'));
				console.log('Adding mapel to schedule:', idx);
				if (!Array.isArray(schedule[idx].mapel)) schedule[idx].mapel = [];
				schedule[idx].mapel.push({ waktu: '', nama: '' });
				render();
				return;
			}
			if (action === 'remove-mapel') {
				const sIdx = parseInt(t.getAttribute('data-sidx'));
				const mIdx = parseInt(t.getAttribute('data-midx'));
				console.log('Removing mapel:', { sIdx, mIdx });
				schedule[sIdx].mapel.splice(mIdx, 1);
				render();
				return;
			}
		});

		addScheduleBtn.addEventListener('click', function(e) {
			e.preventDefault();
			console.log('=== ADD SCHEDULE BUTTON CLICKED ===');
			console.log('Current schedule length:', schedule.length);
			schedule.push({ hari: '', kelas: '', mapel: [] });
			console.log('New schedule length:', schedule.length);
			console.log('Schedule array:', schedule);
			render();
		});
		
		console.log('Add button listener attached to:', addScheduleBtn);

		// Form submit validation and logging
		scheduleForm.addEventListener('submit', (e) => {
			console.log('Form submitting...');
			console.log('Final schedule data:', schedule);
			console.log('Hidden field value:', dataField.value);
			
			// Validate that we have schedule data
			if (schedule.length === 0) {
				e.preventDefault();
				alert('Silakan tambahkan minimal satu baris jadwal.');
				console.error('Form submission blocked: No schedule data');
				return false;
			}
			
			// Validate that dataField has value
			if (!dataField.value || dataField.value === '{}' || dataField.value === '{"schedule":[]}') {
				e.preventDefault();
				alert('Data jadwal kosong. Silakan isi jadwal terlebih dahulu.');
				console.error('Form submission blocked: Empty data field');
				return false;
			}
			
			console.log('Form validation passed, submitting...');
			return true;
		});

		// Initialize date pickers
		if (typeof flatpickr !== 'undefined') {
			document.querySelectorAll('.datepicker').forEach(el => {
				flatpickr(el, { dateFormat: 'Y-m-d' });
			});
			console.log('✓ Flatpickr initialized');
		} else {
			console.warn('Flatpickr not loaded');
		}

		// First render (use existing schedule when editing)
		console.log('Performing initial render...');
		render();
		
		console.log('=== Schedule Manager Initialized Successfully ===');
	}
	
	// Start initialization
	if (document.readyState === 'loading') {
		console.log('Document still loading, waiting for DOMContentLoaded...');
		document.addEventListener('DOMContentLoaded', tryInit);
	} else {
		console.log('Document already loaded, initializing now...');
		tryInit();
	}
	
})();
</script>
@endpush
