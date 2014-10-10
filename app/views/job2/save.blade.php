@extends('layouts.default')
@section('content')
@include('layouts.flashdata')
<div class="col-xs-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>
					
		<div class='tab'>
			<div>
				<div class="panel panel-default">		
					<div class="panel-heading">
						<h4>Save the current template (ensure that the job is refreshed)</h4>
					</div>
					<div class="panel-body">
						{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('JobsController2@postSavet', 'sandbox'), 'method' => 'POST')) }}
							<fieldset>
								
							{{ Form::label('templateType', 'Template type name', 
									array('class' => 'col-xs-5 control-label')) }}
									<div class="input-group col-xs-4">		
									{{ Form::text('templateTypeOwn', Session::get('type_t'), array('class' => 'form-control col-xs-3')) }}
								</div>
						
							{{ Form::label('Overwrite', 'Overwrite?', 
								array('class' => 'col-xs-5 control-label')) }}
							<fieldset>
							
							{{ Form::Radio('overwrite','yes', false, array('id' => 'rb_yes')) }}
							{{ Form::Label('rb_yes', 'yes') }}
<br/>
							
							{{ Form::Radio('overwrite','no',  true, array('id' => 'rb_no')) }}
							{{ Form::Label('rb_no', 'no') }}
						</fieldset>
							</fieldset>
							<br/><br/>
							{{ Form::label('Load', 'Load this template to the current job?', 
								array('class' => 'col-xs-5 control-label')) }}
							<fieldset>
							
							{{ Form::Radio('load','yes', true, array('id' => 'rb_yes')) }}
							{{ Form::Label('rb_yes', 'yes') }}
<br/>
							
							{{ Form::Radio('load','no',  false, array('id' => 'rb_no')) }}
							{{ Form::Label('rb_no', 'no') }}
						</fieldset>
<br/><br/>
						{{ Form::submit('Save', array('class' => 'btn btn-lg btn-primary pull-right', 'style' => 'margin-right:20px')); }}
						{{ Form::close()}}

					</div>
				</div>
			</div>	
		</div>
	</div>
</div>		
@endsection
