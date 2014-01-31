@extends('layouts.default')

@section('content')


<div class="col-xs-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			@include('process.nav')
			@include('layouts.flashdata')
			<div>
				<div class="panel panel-default">		
					<div class="panel-heading">
						<h4>Create your job</h4>
					</div>
					<div class="panel-body">
						{{ Form::model($crowdtask, array('class' => 'form-horizontal crowdtask', 'id'=>'form', 'action' => array('ProcessController@postFormPart', 'platform'), 'method' => 'POST'))}}
						<fieldset>	
							{{ Form::label('title', 'Job title', array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-8">
								{{ Form::text('title', null, array('class' => 'form-control col-xs-6')) }}
							</div>
							<br>
							{{ Form::label('keywords', 'Job keywords', array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-8">
								{{ Form::text('keywords', null, array('class' => 'form-control col-xs-6', 'placeholder' => 'Separated by comma')) }}
							</div>
							<br>
							{{ Form::label('description', 'Job description', array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-8">
								{{ Form::textarea('description', null, array('class' => 'form-control col-xs-6', 'rows'=>'3')) }}
							</div>
							<br>
							{{ Form::label('instructions', 'Job instructions', array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-8">
								{{ Form::textarea('instructions', null, array('class' => 'form-control col-xs-6', 'rows'=>'6')) }}
							</div>
							<br>	
						</fieldset>
						
						<fieldset>
								{{ Form::label('judgmentsPerUnit', 'Judgments per Unit', 
								array('class' => 'col-xs-4 control-label')) }}

							<div class="input-group col-xs-2">
								{{ Form::input('number', 'judgmentsPerUnit',  null, 
									array('onChange' => 'calculate()', 'id'=>'judgmentsPerUnit', 'class' => 'form-control input-sm col-xs-6', 'min' => '1')) }}
							</div>
							<br>
							{{ Form::label('unitsPerTask', 'Units per task', 
								array('class' => 'col-xs-4 control-label')) }}

							<div class="input-group col-xs-2">
								{{ Form::input('number', 'unitsPerTask',  null, 
									array('onChange' => 'calculate()', 'class' => 'form-control input-sm col-xs-6', 'min' => '1')) }}
							</div>
							<br>
							{{ Form::label('expirationInMinutes', 'Duration (minutes)', 
								array('class' => 'col-xs-4 control-label')) }}

							<div class="input-group col-xs-2">
								{{ Form::input('number', 'expirationInMinutes',  null, 
									array('class' => 'form-control input-sm', 'min' => '1')) }}
							</div>

							<br>
							{{ Form::label('reward', 'Reward', array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-2">
							<span class="input-group-addon">$</span> 
							{{ Form::input('number', 'reward',  $crowdtask->reward, array('onChange' => 'calculate()', 'class' => 'form-control input-sm','id'=>'reward',  'min' => '0.01', 'step' => '0.01')) }}
							</div>
							<br>
							{{ Form::label('totalCost', 'Cost per unit', 
									array('class' => 'col-xs-4 control-label')) }}
							<!-- Div totalCost is used for js -->
							<div id="totalCost" class="col-xs-1 control-label"></div>
						</fieldset>
						<br>
						<br>

						<fieldset>
							<legend>Gold questions</legend>	
							@if (count($goldfields)>0)
							{{ Form::label('answerfields[]', 'Gold fields', 
									array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-8">
								@foreach($goldfields as $field)
									{{ Form::checkbox('answerfields[]', $field, null, array('id' => $field))}}
									{{ Form::label($field, "&nbsp;$field")}}<br>
								@endforeach
								<br>Note: if you want to use AMT, specify the action in the platform tab. Crowdflower handles this automatically.
							</div>
							@else
								No _gold fields found in CSV file.
							@endif
						</fieldset>
						<br>
						<br>
						<fieldset>
							<legend>Administration</legend>							
							{{ Form::label('notificationEmail', 'Notification e-mail', array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-8">
								{{ Form::text('notificationEmail', null, array('class' => 'form-control col-xs-6')) }}
							</div>
							<br>
								{{ Form::label('requesterAnnotation', 'Requester Annotation', 
									array('class' => 'col-xs-4 control-label')) }}
								<div class="input-group col-xs-2">
									{{ Form::text('requesterAnnotation',  null, 
										array('class' => 'form-control input-sm')) }}
								</div>
							</fieldset>
						<br>
						<br>
						{{ Form::submit('Next', array('class' => 'btn btn-lg btn-primary pull-right')); }}
						{{ Form::close()}}					
					
				</div>

			</div>
		</div>
	</div>
</div>

@stop