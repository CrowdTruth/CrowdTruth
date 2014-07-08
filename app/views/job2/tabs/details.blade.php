@extends('layouts.default')

@section('content')


<div class="col-xs-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			@include('job2.nav')
			@include('layouts.flashdata')
			<div>
				<div class="panel panel-default">		
					<div class="panel-heading">
						<h4>Create your job</h4>
					</div>
					<div class="panel-body">
						{{ Form::model($jobconf, array('class' => 'form-horizontal jobconf', 'id'=>'form', 'action' => array('JobsController2@postFormPart', 'nextplatform'), 'method' => 'POST'))}}
						<fieldset>	
							<legend>Description</legend>
							{{ Form::label('title', 'Job title', array('class' => 'col-xs-4 control-label', 'data-toggle'=> 'tooltip', 'title'=>'The title of your job, as seen (and searched for) by the worker.')) }}
							<div class="input-group col-xs-8">
								{{ Form::text('title', null, array('class' => 'form-control col-xs-6')) }}
							</div>
							<br>
							{{ Form::label('keywords', 'Job keywords', array('class' => 'col-xs-4 control-label', 'data-toggle'=> 'tooltip', 'title'=>'To be found more easily')) }}
							<div class="input-group col-xs-8">
								{{ Form::text('keywords', null, array('class' => 'form-control col-xs-6', 'placeholder' => 'Separated by comma')) }}
							</div>
							<br>
							{{ Form::label('description', 'Job description', array('class' => 'col-xs-4 control-label', 'data-toggle'=> 'tooltip', 'title'=>'The worker can see this when scrolling through the jobs list.')) }}
							<div class="input-group col-xs-8">
								{{ Form::textarea('description', null, array('class' => 'form-control col-xs-6', 'rows'=>'3')) }}
							</div>
							<br>
							{{ Form::label('instructions', 'Job instructions', array('class' => 'col-xs-4 control-label', 'data-toggle'=> 'tooltip', 'title'=>'Any additional instructions that are not hardcoded in the template.')) }}
							<div class="input-group col-xs-8">
								{{ Form::textarea('instructions', null, array('class' => 'form-control col-xs-6', 'rows'=>'6')) }}
							</div>
							<br>	
						</fieldset>
						
						<fieldset>
							<legend>Count and cost</legend>
								{{ Form::label('workerunitsPerUnit', 'Annotations per Unit',
								array('class' => 'col-xs-4 control-label', 'data-toggle'=> 'tooltip', 'title'=>'The number of different annotations we want for every single unit (usually performed by different workers.')) }}

							<div class="input-group col-xs-2">
								{{ Form::input('number', 'workerunitsPerUnit',  null,
									array('onChange' => 'calculate()', 'id'=>'workerunitsPerUnit', 'class' => 'form-control input-sm col-xs-6', 'min' => '1')) }}
							</div>
							<br>
							{{ Form::label('unitsPerTask', 'Units per task', 
								array('class' => 'col-xs-4 control-label', 'data-toggle'=> 'tooltip', 'title'=>'The number of units that will be displayed on one page.')) }}

							<div class="input-group col-xs-2">
								{{ Form::input('number', 'unitsPerTask',  null, 
									array('onChange' => 'calculate()', 'class' => 'form-control input-sm col-xs-6', 'min' => '1')) }}
							</div>
							<br>
							{{ Form::label('expirationInMinutes', 'Duration (minutes)', 
								array('class' => 'col-xs-4 control-label', 'data-toggle'=> 'tooltip', 'title'=>'The time a worker is allowed to work on a task.')) }}

							<div class="input-group col-xs-2">
								{{ Form::input('number', 'expirationInMinutes',  null, 
									array('class' => 'form-control input-sm', 'min' => '1')) }}
							</div>

							<br>
							{{ Form::label('reward', 'Reward per task', array('class' => 'col-xs-4 control-label', 'data-toggle'=> 'tooltip', 'title'=>'The payment for a completed task.')) }}
							<div class="input-group col-xs-2">
							<span class="input-group-addon">$</span> 
							{{ Form::input('number', 'reward',  null, array('onChange' => 'calculate()', 'class' => 'form-control input-sm','id'=>'reward',  'min' => '0.01', 'step' => '0.01')) }}
							</div>
							<br>

							{{ Form::label('costPerUnit', 'Cost per unit', 
									array('class' => 'col-xs-4 control-label', 'data-toggle' => 'tooltip', 'title' => '(Reward per task / units per task) * annotations per unit')) }}
							<div class="input-group col-xs-2">
							<div id="costPerUnit" class="control-label" style="text-align:left"></div>
							</div>	
							<br>	
							{{ Form::label('totalCost', 'Total (projected) cost', 
									array('class' => 'col-xs-4 control-label', 'data-toggle'=> 'tooltip', 'title'=>'Cost per unit * number of units in the batch')) }}
							<div class="input-group col-xs-2">
							<div id="totalCost" class="control-label" style="text-align:left"></div>
							</div>
							<br>
							{{ Form::label('minRewardPerHour', 'Minimum reward per hour', 
									array('class' => 'col-xs-4 control-label', 'data-toggle' => 'tooltip', 'title' => 'Earnings of a worker if he takes the maximum alotted time.')) }}
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
								No gold units found.
							@endif
						</fieldset>
						<br>
						<br>
						<fieldset>
							<legend>Administration</legend>	
							<!-- TODO: why is this here? -->						
							{{ Form::label('notificationEmail', 'Notification e-mail', array('class' => 'col-xs-4 control-label', 'data-toggle'=> 'tooltip', 'title'=>'Recieve an e-mail on certain events.')) }}
							<div class="input-group col-xs-8">
								{{ Form::text('notificationEmail', null, array('class' => 'form-control col-xs-6')) }}
							</div>
							<br>
							{{ Form::label('eventType', 'Send e-mail on (AMT only!):', 
									array('class' => 'col-xs-4 control-label')) }}
								<div class="input-group col-xs-2">
									{{ Form::select('eventType',  array('AssignmentAccepted' => 'AssignmentAccepted', 'AssignmentAbandoned' => 'AssignmentAbandoned', 'AssignmentReturned' => 'AssignmentReturned', 'AssignmentSubmitted' => 'AssignmentSubmitted', 'HITReviewable' => 'HITReviewable', 'HITExpired'=>'HITExpired'), null, array('class' => 'selectpicker', 'data-toggle'=> 'tooltip', 'title'=>'')) }}
								</div>
							<!--note: removed RequesterWorkerunit as it doesn't show up in the response (and we can have our tags locally)-->
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
