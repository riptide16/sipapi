<!DOCTYPE html>
<html>
<head>
	<title>Onthespot</title>
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
					<th>Komponen</th>
					<th>Skor Huruf</th>
					<th>Skor Angka</th>
				</tr>
			</thead>
			<tbody>
				@php $index=1 @endphp
				@foreach($data['component'] as $component)
					<tr>
						<td>{{ $index++ }}. {{ $component->name }}</td>
					</tr>
					@php $i=1 @endphp
					@foreach($data['evaluationContent'] as $row)
					@if($row->name == $component->name)
					<tr>
						<td>{{ $i++ }}</td>
						@if($row->value == '5')
						<td>A</td>
						@elseif($row->value == '4')
						<td>B</td>
						@elseif($row->value == '3')
						<td>C</td>
						@elseif($row->value == '2')
						<td>D</td>
						@else
						<td>E</td>
						@endif
						<td>{{$row->value}}</td>
					</tr>
					@endif
					@endforeach
				@endforeach
			</tbody>
		</table>
	</div>
 
</body>
</html>
