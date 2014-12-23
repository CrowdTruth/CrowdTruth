@extends('layouts.default_new')
@section('title','Userlist')
@section('content')
			<!-- START /index --> 			
<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			<div class='title'>
				<h2>Userlist</h2>
			</div>
			@include('layouts.flashdata')
			<div class='row'>
				<div class="col-xs-12"  style="padding-bottom:40px; padding-top:20px">
					<table class="table table-striped" style='width:100%'>
						<tr>
							<th>Name</th>
							<th>Username</th>
							<th>Email</th>
							<th>Role</th>
							<th>Groups</th>
						</tr>
						@foreach ($userlist as $user)
						<tr class='text-left' >
							<td>{{ link_to('user/' . $user['_id'], $user['firstname'] . ' ' . $user['lastname']) }}</td>
							<td>{{ link_to('user/' . $user['_id'], $user['_id']) }}</td>
							<td>{{ $user['email'] }}</td>
							<td><small>Administrator</small></td>
							<td>{{ link_to('group/ibm', 'IBM') }}, {{ link_to('group/biographynet', 'BiographyNet') }}</td>
						</tr>
						@endforeach
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@stop