@extends('layouts.default_new')
@section('title','Profile Settings')
@section('content')

<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			@include('user.nav', array('user'=>$user))
			@include('layouts.flashdata')
			<div>

				{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('JobsController2@postFormPart', 'submit'), 'method' => 'POST'))}}
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
						Groups
					</div>
					<div class="panel-body">
						<div class="form-group">
							{{ Form::label('name', 'IBM', [ 'class' => 'col-xs-4 control-label' ]) }}
							<div class='col-xs-2'>
								{{ Form::select('role',  ['Administrator','Member','Guest'], null, array('class' => 'selectpicker', 'data-container' =>'body',  'data-toggle'=> 'tooltip')) }}
							</div>
							<div class='col-xs-2'>
								<p class="form-control-static"><a class="btn btn-danger btn-sm" href="#" data-toggle="tooltip" data-placement="top" title="Leave Group"><i class="fa fa-remove"></i></a></p>
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('name', 'BiographyNet', [ 'class' => 'col-xs-4 control-label' ]) }}
							<div class='col-xs-2'>
								{{ Form::select('role',  ['Administrator','Member','Guest'], null, array('class' => 'selectpicker', 'data-container' =>'body',  'data-toggle'=> 'tooltip')) }}
							</div>
							<div class='col-xs-2'>
								<p class="form-control-static"><a class="btn btn-danger btn-sm" href="#" data-toggle="tooltip" data-placement="top" title="Leave Group"><i class="fa fa-remove"></i></a></p>
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
}
@endsection
