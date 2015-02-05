@extends('layouts.default_new')

@section('head')
@stop

@section('title','Profile Settings')
@section('content')

<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>
		<div class='tab'>	
			@include('layouts.flashdata')
			<div>
				{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('GroupController@createProject', 'submit'), 'method' => 'POST'))}}
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3>Create new Project</h3>
					</div>
					<div class="panel-body">
						<div class="form-group has-feedback">
							{{ Form::label('name', 'Project Name', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-5'>
								<input type="text" class="form-control" id="inputSuccess2" placeholder="Name" aria-describedby="inputSuccess2Status">
								<span class="glyphicon glyphicon-ok form-control-feedback success hidden" aria-hidden="true"></span>
								<span class="glyphicon glyphicon-remove form-control-feedback error hidden" aria-hidden="true"></span>
							</div>
						</div>
						<div class="form-group has-feedback">
							{{ Form::label('alias', 'Alias', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-5'>
								<input type="text" class="form-control" id="inputSuccess2" placeholder="Alias" aria-describedby="inputSuccess2Status">
								<span class="glyphicon glyphicon-ok form-control-feedback success hidden" aria-hidden="true"></span>
								<span class="glyphicon glyphicon-remove form-control-feedback error hidden" aria-hidden="true"></span>
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('description', 'Project description', [ 'class' => 'col-xs-3 control-label' ]) }}
							<div class='col-xs-4'>
								{{ Form::textarea('description', null, array('class' => 'form-control', 'placeholder' => 'Description')) }}
							</div>
						</div>
					</div>
					<div class="panel-footer">
						{{ Form::submit('Create Project', array('class' => 'btn btn-primary pull-right')); }}
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
$('.has-feedback input').on('input', function() {
	if($(this).val().length > 1) {
		$(this).parents('.has-feedback').addClass('has-success');
		$(this).siblings('.success').removeClass('hidden');
	}
});
</script>

@stop
