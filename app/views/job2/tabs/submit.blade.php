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
									array('class' => 'col-xs-5 control-label')) }}
								<div class="input-group col-xs-3">
									{{ Form::select('title',  array('Images standard' => 'Images standard', 'A big task' => 'A big task'), null, array('class' => 'selectpicker', 'data-toggle'=> 'tooltip', 'title'=>'')) }}
								</div><div class="input-group col-xs-3">
									{{ Form::text('titleOwn', null, array('class' => 'form-control col-xs-2')) }}
								</div>
							
								
							
							<br/><br/>
							{{ Form::label('templateType', 'Select a template-type from the set of predefined ones or give your own', 
									array('class' => 'col-xs-5 control-label')) }}
								<div class="input-group col-xs-3">
									{{ Form::select('templateType',  array('RelEx' => 'RelEx', 'Image tagging' => 'Image tagging'), null, array('class' => 'selectpicker', 'data-toggle'=> 'tooltip', 'templateType'=>'')) }}		
									</div><div class="input-group col-xs-3">
									{{ Form::text('templateTypeOwn', null, array('class' => 'form-control col-xs-2')) }}
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
