@extends('layouts.default')
@section('content')

<div class="col-xs-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			@include('job2.nav')
			<div>
				<div class="panel panel-default">		
					<div class="panel-heading">
						<h4>Preview task and submit</h4>
					</div>
					<div class="panel-body">
						{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('JobsController2@postSubmitFinal', 'sandbox'), 'method' => 'POST')) }}
							<fieldset>
							{{ Form::label('title', 'Select a title from the set of predefined ones or give your own', 
									array('class' => 'col-xs-9 control-label')) }}
								<div class="input-group col-xs-2">
									{{ Form::select('title',  array('Images standard' => 'Images standard', 'A big task' => 'A big task'), null, array('class' => 'selectpicker', 'data-toggle'=> 'tooltip', 'title'=>'')) }}
								</div>
							<br/><br/>
							{{ Form::label('templateType', 'Select a template-type from the set of predefined ones or give your own', 
									array('class' => 'col-xs-9 control-label')) }}
								<div class="input-group col-xs-2">
									{{ Form::select('templateType',  array('RelEx' => 'RelEx', 'Image tagging' => 'Image tagging'), null, array('class' => 'selectpicker', 'data-toggle'=> 'tooltip', 'templateType'=>'')) }}		
								</div>
							<br/>
							</fieldset>
	

						{{ Form::submit('Create Job', array('class' => 'btn btn-lg btn-default pull-right', 'style' => 'margin-right:20px')); }}
						{{ Form::close()}}




					</div>
				</div>
			</div>	
		</div>
	</div>
</div>		
@endsection

@section("modal")
<!-- HIDDEN -->
<div class="modal fade" id="myModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Save job details</h4>
			</div>
			<div class="modal-body">
				<style type="text/css">
				.jstree li > a > .jstree-icon {  display:none !important; } 
				</style>
				<div id="jstree"></div>
				<br>
				<div>
					{{ Form::open(array('action' => 'JobsController2@postSaveDetails'))}}
					{{ Form::label('template', 'Pick a name. Use underscores instead of spaces. NB: there needs to be an HTML template for every jobdetails file.') }}
					<br>
					{{ Form::text('template', $template, array('id' => 'template', 'class' => 'form-control col-xs-6')) }}
				</div>
			</div>
			<br>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				{{ Form::submit('Save', array('class' => 'btn btn-primary'))}}       
				{{ Form::close()}}
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- /HIDDEN -->
@endsection
@stop