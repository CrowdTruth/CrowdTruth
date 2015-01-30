@extends('layouts.default_new')
@section('title','Activity')
@section('content')
			<!-- START /index --> 			
<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			@include('user.nav', array('user'=>$user))
			@include('layouts.flashdata')
			<div class='row'>
				<div class="col-xs-12"  style="padding-bottom:40px; padding-top:20px">
					<table class="table table-striped" style='width:100%'>
						<tr>
							<th>Time</th>
							<th>Entity</th>
							<th>Activity</th>
						</tr>
						@foreach ($activities as $activity)
						<tr class='text-left' >
							<td>{{ $activity['updated_at'] }}</td>
							<td>{{ $activity['_id'] }}</td>
							<td>{{ $activity['softwareAgent_id'] }}</td>
						</tr>
						@endforeach
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@stop