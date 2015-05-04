@extends('layouts.default_new')
@section('title','Profile Settings')
@section('content')

<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			@include('users.nav', array('user'=>$user))
			@include('layouts.flashdata')
			<div>
				{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('UserController@postProfile', 'submit'), 'method' => 'POST'))}}
				<div class="panel panel-default">
					<div class="panel-heading">
						Profile settings
					</div>
					<div class="panel-body">
						<div class="form-group">
							{{ Form::label('useHeaders', 'Username', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-3'>
								<p class="form-control-static">{{ $user['_id'] }}</p>
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('name', 'Name', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-3'>
								{{ Form::text('name', $user['firstname'], array('class' => 'form-control', 'placeholder' => 'Firstname')) }}
							</div>
							<div class='col-xs-4'>
								{{ Form::text('name', $user['lastname'], array('class' => 'form-control', 'placeholder' => 'Lastname')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('email', 'Email', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-5'>
								{{ Form::text('email', $user['email'], array('class' => 'form-control', 'placeholder' => 'Email')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('email', 'Groups', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-3'>
								@foreach($groups as $group)
									{{ link_to('group/'.$group['name'], $group['name']) }} <small>({{ $group['role'] }})</small>
								@endforeach
							</div>
						</div>
					</div>
					<div class="panel-footer">
						{{ Form::submit('Save', array('class' => 'btn btn-primary pull-right')); }}
						<div class='clearfix'></div>
					</div>
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading">
						Change Password
					</div>
					<div class="panel-body">
						<div class="form-group">
							{{ Form::label('name', 'Current Password', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-4'>
								{{ Form::password('name', array('class' => 'form-control', 'placeholder' => 'Current Password')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('newpassword', 'New Password', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-4'>
								{{ Form::password('newpassword', array('class' => 'form-control', 'placeholder' => 'New Password')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('newpassword', 'Repeat New Password', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-4'>
								{{ Form::password('newpassword', array('class' => 'form-control', 'placeholder' => 'Repeat Password')) }}
							</div>
						</div>
					</div>
					<div class="panel-footer">
						{{ Form::submit('Change', array('class' => 'btn btn-primary pull-right')); }}
						<div class='clearfix'></div>
					</div>
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
$(document).ready(function() {
$('.selectpicker').selectpicker();
});
</script>
@endsection
