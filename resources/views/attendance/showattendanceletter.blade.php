<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Preview Surat - {{ $letterName }} - {{ $student->name }}</title>
	<style>
		body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 24px; color: #111827; }
		.header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 16px; }
		.title { font-size: 20px; font-weight: 600; }
		.meta { color: #6b7280; font-size: 14px; }
		.card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-top: 12px; }
		.badge { display: inline-block; background: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe; padding: 2px 8px; border-radius: 999px; font-size: 12px; }
		.actions { margin-top: 20px; display: flex; gap: 8px; }
		.btn { display: inline-block; padding: 10px 14px; border-radius: 6px; text-decoration: none; font-weight: 600; }
		.btn-primary { background: #2563eb; color: white; }
		.btn-secondary { background: #f3f4f6; color: #111827; border: 1px solid #e5e7eb; }
		.preview { white-space: pre-wrap; line-height: 1.6; }
		.note { background: #fff7ed; border: 1px solid #fdba74; color: #9a3412; padding: 10px 12px; border-radius: 6px; }
		table { width: 100%; border-collapse: collapse; }
		th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
		th { background: #f9fafb; }
	</style>
</head>
<body>
	<div class="header">
		<div class="title">Preview Surat: {{ $letterName }}</div>
		<div class="meta">Siswa: <strong>{{ $student->name }}</strong> · Tipe: <span class="badge">{{ strtoupper($type) }}</span></div>
	</div>

	@if(!empty($html))
		<div class="card">
			<div class="meta" style="margin-bottom:8px;">Template HTML ditampilkan dengan data terisi.</div>
			<div class="preview">{!! $html !!}</div>
		</div>
	@elseif(!empty($text))
		<div class="card">
			<div class="meta" style="margin-bottom:8px;">Template teks ditampilkan dengan data terisi.</div>
			<div class="preview">{{ $text }}</div>
		</div>
	@elseif(!empty($docxPreviewUnsupported))
		<div class="card">
			<div class="note">Preview untuk file DOCX belum didukung di browser. Anda tetap dapat mengunduh surat dengan data terisi menggunakan tombol di bawah.</div>
			<div style="margin-top:12px;" class="meta">Ringkasan data yang akan dimasukkan:</div>
			<table style="margin-top:8px;">
				<thead>
					<tr>
						<th>Placeholder</th>
						<th>Nilai</th>
					</tr>
				</thead>
				<tbody>
				@foreach($replacements as $k => $v)
					<tr>
						<td>{{ $k }}</td>
						<td>{{ is_scalar($v) ? $v : json_encode($v) }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	@else
		<div class="card">
			<div class="note">Tidak ada konten yang dapat dipreview untuk template ini. Silakan gunakan tombol Unduh untuk mendapatkan surat.</div>
		</div>
	@endif

	<div class="actions">
		<a class="btn btn-primary" href="{{ $downloadUrl }}">Unduh Surat</a>
		<a class="btn btn-secondary" href="{{ url()->previous() }}">Kembali</a>
	</div>
</body>
</html>
