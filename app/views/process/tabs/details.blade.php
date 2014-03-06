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
						{{ Form::model($jobconf, array('class' => 'form-horizontal jobconf', 'id'=>'form', 'action' => array('ProcessController@postFormPart', 'platform'), 'method' => 'POST'))}}
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
								{{ Form::label('annotationsPerUnit', 'Annotations per Unit', 
								array('class' => 'col-xs-4 control-label')) }}

							<div class="input-group col-xs-2">
								{{ Form::input('number', 'annotationsPerUnit',  null, 
									array('onChange' => 'calculate()', 'id'=>'annotationsPerUnit', 'class' => 'form-control input-sm col-xs-6', 'min' => '1')) }}
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
							{{ Form::label('reward', 'Reward per task', array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-2">
							<span class="input-group-addon">$</span> 
							{{ Form::input('number', 'reward',  null, array('onChange' => 'calculate()', 'class' => 'form-control input-sm','id'=>'reward',  'min' => '0.01', 'step' => '0.01')) }}
							</div>
							<br>

							{{ Form::label('costPerUnit', 'Cost per unit', 
									array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-2">
							<div id="costPerUnit" class="control-label" style="text-align:left"></div>
							</div>	
							<br>	
							{{ Form::label('totalCost', 'Total (projected) cost', 
									array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-2">
							<div id="totalCost" class="control-label" style="text-align:left"></div>
							</div>
							<br>
							{{ Form::label('minRewardPerHour', 'Minimum reward per hour', 
									array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-2">
							<div id="minRewardPerHour" class="control-label" style="text-align:left"></div>
							</div>	
							<br>	

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
							{{ Form::label('eventType', 'Send e-mail on (AMT only!):', 
									array('class' => 'col-xs-4 control-label')) }}
								<div class="input-group col-xs-2">
									{{ Form::select('eventType',  array('AssignmentAccepted' => 'AssignmentAccepted', 'AssignmentAbandoned' => 'AssignmentAbandoned', 'AssignmentReturned' => 'AssignmentReturned', 'AssignmentSubmitted' => 'AssignmentSubmitted', 'HITReviewable' => 'HITReviewable', 'HITExpired'=>'HITExpired'), null, array('class' => 'selectpicker')) }}
								</div>
							<!--note: removed RequesterAnnotation as it doesn't show up in the response (and we can have our tags locally)-->
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