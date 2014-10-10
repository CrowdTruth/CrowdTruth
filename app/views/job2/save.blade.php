@extends('layouts.default_new')
@section('content')
@include('layouts.flashdata')
<div class="col-xs-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>
					
		<div class='tab'>
			<div>
				<div class="panel panel-default">		
					<div class="panel-heading">
						<h4>Save the current template</h4>
					</div>
					<div class="panel-body">
						{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('JobsController2@postSavet', 'sandbox'), 'method' => 'POST')) }}
						<fieldset>
								
							{{ Form::label('templateType', 'New template name:', 
									array('class' => 'col-xs-4 control-label')) }}
								<div class="input-group col-xs-3">		
									{{ Form::text('templateType', Session::get('type_t'), array('class' => 'form-control col-xs-3')) }}
								</div>
						
							{{ Form::label('Allow', 'Overwrite existing template with this one in the Template Library?', 
								array('class' => 'col-xs-4 control-label')) }}
							
							
							{{ Form::Checkbox('overwrite','yes', false, array('id' => 'rb_yes')) }}
						

							
						</fieldset>
	
					   
							<br/><br/>
							{{ Form::label('Load', 'Current job:', 
								array('class' => 'col-xs-4 control-label')) }}
							<fieldset>
							
							{{ Form::Radio('load','yes', true, array('id' => 'rb_yes')) }}
							{{ Form::Label('rb_yes', 'Update it with the above new template') }}
<br/>
							
							{{ Form::Radio('load','no',  false, array('id' => 'rb_no')) }}
							{{ Form::Label('rb_no', 'Keep the old template') }}
						
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
