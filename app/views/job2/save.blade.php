@extends('layouts.default_new')
@section('content')
@include('layouts.flashdata')
<div class="col-xs-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>
					
		<div class='tab'>
			<div>
				<div class="panel panel-default">		
					<div class="panel-heading">
						<h4>Save the current template as a new one</h4>
					</div>
					<div class="panel-body">
						{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('JobsController2@postSavet', 'sandbox'), 'method' => 'POST')) }}
						<fieldset>
								
							{{ Form::label('templateType', 'Save as:', 
									array('class' => 'col-xs-6 control-label')) }}
								<div class="input-group col-xs-3">		
									{{ Form::text('templateType', Session::get('type_t'), array('class' => 'form-control col-xs-3')) }}
								</div>
						
							{{ Form::label('Load', 'Load this new template to the current job?', 
								array('class' => 'col-xs-6 control-label')) }}
							
							
							{{ Form::Checkbox('load','yes', false) }}
						
							<?php 
							echo "{{ Form::Checkbox('overwrite','yes', false)}} ";
							//if(isset($overw)) echo "Form::Checkbox('overwrite','yes', false)";
							?>
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
