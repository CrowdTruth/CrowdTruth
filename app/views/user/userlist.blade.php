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
							<th>Groups(role)</th>
						</tr>

						<?php use \MongoDB\Security\GroupHandler as GroupHandler; ?>
						<?php use \MongoDB\Security\PermissionHandler as PermissionHandler; ?>
						<?php use \MongoDB\Security\Permissions as Permissions; ?>
						
						@foreach ($userlist as $user)
						<tr class='text-left' >
							<td>{{ $user['firstname'] }} {{ $user['lastname'] }}</td>
							<td>{{ $viewProfiles?link_to('user/' . $user['_id'], $user['_id']):$user['_id'] }}</td>
							<td>{{ $user['email'] }}</td>
							<td>
							@foreach (GroupHandler::getUserGroups($user) as $group)
								{{ (PermissionHandler::checkGroup(Auth::user(), $group['name'], Permissions::GROUP_READ))?link_to('group/'.$group['name'], $group['name']):$group['name'] }} <small>({{ $group['role'] }})</small>
							@endforeach
							</td>
						</tr>
						@endforeach
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@stop