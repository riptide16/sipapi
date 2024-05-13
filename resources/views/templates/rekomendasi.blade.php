<!DOCTYPE html>
<html>
<head>
	<title>Rekomendasi</title>
</head>
<body>
 
	<div class="container">
		<center>
			<h4>AKREDITASI PERPUSTAKAAN {{ strtoupper($data['accreditationData']->institution->category) }}</h4>
			<h4>PERPUSTAKAAN {{ strtoupper($data['accreditationData']->institution->library_name) }}</h4>
		</center>
		
		<table class='table table-bordered'>
			<thead>
				<tr>
					<th rowspan="2">NO</th>
					<th rowspan="2">KOMPONEN</th>
					<th rowspan="2">BOBOT</th>
					<th rowspan="2">NILAI</th>
					<th colspan="2">NILAI AKREDITASI</th>
					<th rowspan="2">REKOMENDASI</th>
				</tr>
				<tr>
					<th>%</th>
					<th>HURUF</th>	
				</tr>
			</thead>
			<tbody>
				@php 
					$index=1;
					$total_value = 0;  
				@endphp
				@foreach($data['recommendations'] as $row)
				<tr>
					<td>{{ $index++ }}</td>
					<td>{{ $row['name'] }}</td>
					<td>{{ $row['weight'] }}</td>
					<td>{{ $row['score'] }}</td>
					<td>{{ $row['percentage'] }}</td>
					<td></td>
					<td>{{ $row['content'] }}</td>
				</tr>
				@php $total_value += $row['score'] @endphp
				@endforeach
				<tr>
					<td></td>
					<td>Jumlah</td>
					<td>{{ $data['accreditationData']['finalResult']['weight'] }}</td>
					<td>{{ $total_value }}</td>
					<td>{{ $total_value/$data['accreditationData']['finalResult']['weight']*100 }}</td>
					<td><b>Terakreditasi {{ $data['predicate'] }}</b></td>
				</tr>
			</tbody>
		</table>
	</div>
 
</body>
</html>
