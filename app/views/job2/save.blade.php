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
						{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('JobsController2@postSave', 'sandbox'), 'method' => 'POST')) }}
						<fieldset>
								
							{{ Form::label('templateType', 'Save as:', array('class' => 'col-xs-5 control-label')) }}
								<div class="input-group col-xs-3">		
									{{ Form::text('templateType', Session::get('type_t'), array('class' => 'form-control col-xs-2')) }}
								</div>
							{{ Form::label('templateDescription', 'Template Description:', array('class' => 'col-xs-5 control-label')) }}
								<div class="input-group col-xs-3">		
									{{ Form::text('templateDescription', '', array('class' => 'form-control col-xs-2')) }}
								</div>
<hr>
							{{ Form::label('templateFields', 'Input Fields:', array('class' => 'col-xs-5 control-label')) }}
								<div class="input-group col-xs-3">	
									@foreach (Session::get('templateFields') as $field)
										<div class="input-group col-xs-3">	
    										{{ Form::label('label' . $field, $field, array('class' => 'col-xs-3 control-label')) }}
    										<p></p>{{ Form::text('description' . $field, '', array('class' => 'form-control col-xs-4')) }}
    										{{ Form::select('type' . $field,  array('' => 'Select field type', 'string' => 'String', 'number' => 'Number', 'url' => 'URL', 'offsets' => 'Offsets'), array('class' => 'selectpicker', null, 'id' => 'labelType' . $field, 'data-width' => '100%', 'data-container' => 'body', 'data-toggle'=> 'tooltip')) }}
    										{{ Form::select('noitems' . $field,  array('' => 'Select the number of items', 'single' => 'Single', 'multiple' => 'Multiple'), array('class' => 'selectpicker', null, 'id' => 'labelnoitems' . $field, 'data-width' => '100%', 'data-container' => 'body', 'data-toggle'=> 'tooltip')) }}
    									</div>
									@endforeach
								</div>
<hr>
							{{ Form::label('resultField', 'Result Field:', array('class' => 'col-xs-5 control-label')) }}
								<div class="input-group col-xs-3">	
									{{ Form::label('resultFieldName', 'Name: ', array('class' => 'col-xs-3 control-label')) }}
    								<p></p><p></p>{{ Form::text('resultFieldName', '', array('class' => 'form-control col-xs-4')) }}
    								<p></p>{{ Form::label('resultFieldDescription', 'Description: ', array('class' => 'col-xs-3 control-label')) }}
    								<p></p><p></p>{{ Form::text('resultFieldDescription', '', array('class' => 'form-control col-xs-4')) }}
    								<p></p>{{ Form::select('typeResult',  array('' => 'Select result type', 'string' => 'String', 'number' => 'Number', 'url' => 'URL', 'offsets' => 'Offsets'), array('class' => 'selectpicker', null, 'id' => 'labelResultType', 'data-width' => '100%', 'data-container' => 'body', 'data-toggle'=> 'tooltip')) }}
    								<p></p>{{ Form::select('noitemsResult',  array('' => 'Select result number of items', 'single' => 'Single', 'multiple' => 'Multiple'), array('class' => 'selectpicker', null, 'id' => 'labelResultNoItems', 'data-width' => '100%', 'data-container' => 'body', 'data-toggle'=> 'tooltip')) }}
    							</div>
<hr>								
							{{ Form::label('Load', 'Load this new template to the current job?', 
								array('class' => 'col-xs-5 control-label')) }}
							
							
							{{ Form::Checkbox('load','yes', false) }}
						
						
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
