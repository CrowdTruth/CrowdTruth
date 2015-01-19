@extends('layouts.default_new')

@section('head')
<script>
function addUser() {
	formData = $("#changeUserForm").serialize();
	$.ajax({
		type: 'POST',
		url : '{{ URL::action('UserController@addUserToGroup') }}',
		data: formData,
		success: function (data) {
			$("#newUserName").val('');
			if(data['status']=='ok') {
				 location.reload(); 
			}
		}
	});
}

</script>
@stop

@section('title','Profile Settings')
@section('content')

<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>
		<div class='tab'>
			<div class='title'>
				<h2>Group: {{ $groupName }}</h2>
			</div>
		
			@include('layouts.flashdata')
			<div>
				{{ Form::open([ 'class' => 'form-horizontal jobconf', 'id' => 'changeUserForm' ] ) }}
				{{ Form::hidden('groupName', $groupName) }}
				<div class="panel panel-default">
					<div class="panel-heading">
						Members
					</div>
					<div class="panel-body" id="userRolePanel">
					@foreach($groupUsers as $role => $users)
						@foreach($users as $user)
							<div class="form-group">
								{{ Form::label('name', $user, [ 'class' => 'col-xs-4 control-label' ]) }}
							@if($canEditGroup)
								<div class='col-xs-2'>
									{{ Form::select('role',  [ 'admin' => 'Administrator', 'member' => 'Member', 'guest' => 'Guest'], $role, 
										[  'data-container' =>'body',  'data-toggle'=> 'tooltip' ]) }}
								</div>
								<div class='col-xs-2'>
									<p class="form-control-static"><a class="btn btn-danger btn-sm" href="#" data-toggle="tooltip" data-placement="top" title="Remove user"><i class="fa fa-remove"></i></a></p>
								</div>
							@else
								<div class='col-xs-2'>
									{{ Form::label('role', $role, [ 'class' => 'col-xs-4 control-label' ]) }}
								</div>
							@endif
							</div>
						@endforeach
					@endforeach

					@if($canEditGroup)
						<div class="form-group">
							{{ Form::label('addNew', 'Add new', [ 'class' => 'col-xs-4 control-label' ]) }}
							<div class='col-xs-2'>
								{{ Form::text('newUserName',  '', [  'class' => 'form-control', 'id' => 'newUserName' ]) }}
							</div>
							<div class='col-xs-2'>
								<p class="form-control-static"><a class="btn btn-primary btn-sm" href="#" data-toggle="tooltip" data-placement="top" title="Add user" onClick="addUser()"><i class="fa fa-plus"></i></a></p>
							</div>
						</div>
					@endif
					</div>

					@if($canEditGroup)
					<div class="panel-footer">
						{{ Form::submit('Change', array('class' => 'btn btn-primary pull-right')); }}
						<div class='clearfix'></div>
					</div>
					@endif
				</div>
				{{ Form::close() }}
				
				
				{{ Form::open([ 'class' => 'form-horizontal jobconf', 'action' => 'UserController@addUserToGroup' ] ) }}
				{{ Form::submit('Change', array('class' => 'btn btn-primary pull-right')); }}
				{{ Form::close() }}
				
				@if($canEditGroup)
				{{ Form::open([ 'class' => 'form-horizontal jobconf' ] ) }}
				<div class="panel panel-default">
					<div class="panel-heading">
						Invitation codes
					</div>
					<div class="panel-body">
						<div class="form-group">
							{{ Form::label('admins', 'Administrators', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-3'>
								{{ Form::text('adminsICode', $inviteCodes['admin'], [ 'class' => 'form-control', 'placeholder' => 'Firstname' ] ) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('members', 'Members', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-3'>
								{{ Form::text('membersICode', $inviteCodes['member'], [ 'class' => 'form-control', 'placeholder' => 'Firstname' ] ) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('guests', 'Guests', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-3'>
								{{ Form::text('guestsICode', $inviteCodes['guest'], [ 'class' => 'form-control', 'placeholder' => 'Firstname' ] ) }}
							</div>
						</div>
					</div>
					<div class="panel-footer">
						{{ Form::submit('Change', array('class' => 'btn btn-primary pull-right')); }}
						<div class='clearfix'></div>
					</div>
				</div>
				{{ Form::close() }}
				@endif
				
				{{ Form::open([ 'class' => 'form-horizontal jobconf' ] ) }}
				<div class="panel panel-default">
					<div class="panel-heading">
						Crowdflower account
					</div>
					<div class="panel-body">
						<div class="form-group">
							{{ Form::label('cfUsername', 'Username', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-3'>
								{{ Form::text('cfUsername', 'Username', [ 'class' => 'form-control', 'placeholder' => 'Username' ] ) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('cfPassword', 'cfPassword', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-3'>
								{{ Form::password('cfPassword', [ 'class' => 'form-control', 'placeholder' => 'password' ] ) }}
							</div>
						</div>
					</div>
					@if($canEditGroup)
					<div class="panel-footer">
						{{ Form::submit('Change', array('class' => 'btn btn-primary pull-right')); }}
						<div class='clearfix'></div>
					</div>
					@endif
				</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
@stop

@section('end_javascript')
{{ javascript_include_tag('bootstrap-select.js') }}

<script>
</script>

@stop
