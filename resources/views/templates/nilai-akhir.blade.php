<!DOCTYPE html>
<html>
<head>
	<title>Nilai Akhir</title>
</head>
<body>
 
	<div class="container">
		<center>
			<h4>NILAI AKHIR AKREDITASI PERPUSTAKAAN {{ strtoupper($data['accreditationData']->institution->library_name) }}</h4>
		</center>
		
		<table class='table table-bordered'>
			<thead>
				<tr>
					<th>No.</th>
					<th>Komponen</th>
					<th>Jumlah Skor</th>
					<th>Jumlah Soal</th>
					<th>Bobot</th>
					<th>Nilai</th>
				</tr>
			</thead>
			<tbody>
				@php $index=1 @endphp
				@foreach($data['accreditationResult'] as $component)
				<tr>
					<td>{{ $index++ }}</td>
					<td>{{ $component['instrument_component'] }}</td>
					<td>{{ round($component['total_value'], 2) }}</td>
					<td>{{ $component['total_instrument'] }}</td>
					<td>{{ $component['weight'] }}</td>
					<td>{{ $component['score'] }}</td>
				</tr>
				@endforeach
				<tr>
					<td></td>
					<td>Jumlah</td>
					<td>{{ round($data['accreditationData']['finalResult']['total_value'], 2) }}</td>
					<td>{{ $data['accreditationData']['finalResult']['total_instrument'] }}</td>
					<td>{{ $data['accreditationData']['finalResult']['weight'] }}</td>
					<td>{{ round($data['accreditationData']['finalResult']['score'], 2) }}</td>
				</tr>
				<tr>
					<td>NILAI PERPUSTAKAAN {{ strtoupper($data['accreditationData']->institution->library_name) }}</td>
					<td></td>
					<td></td>
					<td>{{ round($data['accreditationData']['finalResult']['score'], 2) }}</td>
				</tr>
			</tbody>
		</table>
		<table class='table table-borderless'>
			<tbody>
				<tr><td><b>Terakreditasi {{ $data['predicate'] }}</b></td></tr>
				<tr><td>Keterangan :              <i>Rumus Nilai= Jumlah skor : (jumlah soal X 5) X bobot</i></td></tr>
				<tr><td>1. Akreditasi A (Baik Sekali), bila Jumlah Skor (91 ≤ NA ≤ 100)</td></tr>
				<tr><td>2. Akreditasi B (Baik), bila Jumlah Skor (76 ≤ NA ≤ 90)</td></tr>
				<tr><td>3. Akreditasi C (Cukup Baik), bila Jumlah Skor (60 ≤ NA ≤ 75)</td></tr>
				<tr><td>4. Tidak Terakreditasi, bila Jumlah Skor (NA Kurang dari 60 )</td></tr>
			</tbody>
		</table>
	</div>
 
</body>
</html>
