@extends('layouts.default_new')

@section('head')
@stop

@section('title','Profile Settings')
@section('content')

<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>
		<div class='tab'>
			@include('projects.nav', array('project'=>$project))
			@include('layouts.flashdata')
			<div class='title'>
				<h2>Project: {{ $project }}</h2>
			</div>
			<div>
				<div class="panel panel-default">
					<div class="panel-heading">
						Members (only informative list -- maybe we can even remove it).
					</div>
					<div class="panel-body" id="userRolePanel">
						<ul class="list-group">
						@foreach($users as $role => $usergroup)
							@foreach($usergroup as $user)
							<li class="list-group-item">{{ $user }} ({{ $role }})</li>
							@endforeach
						@endforeach
						</ul>
					</div>
				</div>
				
				@if($canEditGroup)
				{{ Form::open([ 'action' => [ 'ProjectController@updateInviteCodes', $project ], 'class' => 'form-horizontal jobconf' ] ) }}
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
				
				{{ Form::open([ 'action' => [ 'ProjectController@updateAccountCredentials', $project ], 'class' => 'form-horizontal jobconf' ] ) }}
				<div class="panel panel-default">
					<div class="panel-heading">
						Crowdflower account
					</div>
					<div class="panel-body">
						<div class="form-group">
							{{ Form::label('cfUsername', 'Username', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-3'>
								{{ Form::text('cfUsername', $credentials['cfUsername'], [ 'class' => 'form-control', 'placeholder' => 'Username' ] ) }}
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
