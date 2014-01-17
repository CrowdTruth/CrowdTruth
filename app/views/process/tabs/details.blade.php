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
						<h4>Create your AMT-job</h4>
					</div>
					<div class="panel-body">
						{{ Form::model($crowdtask, array('class' => 'form-horizontal crowdtask', 'action' => array('ProcessController@postFormPart', 'platform'), 'method' => 'POST'))}}
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
						</fieldset>
						
						<fieldset>
								{{ Form::label('maxassignments', 'Max assignments', 
								array('class' => 'col-xs-4 control-label')) }}

							<div class="input-group col-xs-2">
								{{ Form::input('number', 'maxassignments',  $crowdtask->maxAssignments, 
									array('class' => 'form-control input-sm col-xs-6', 'min' => '1')) }}
							</div>
							<br>
							{{ Form::label('assignmentdurationinseconds', 'Assignment duration (seconds)', 
								array('class' => 'col-xs-4 control-label')) }}

							<div class="input-group col-xs-2">
								{{ Form::input('number', 'assignmentdurationinseconds',  $crowdtask->assignmentDurationInSeconds, 
									array('class' => 'form-control input-sm', 'min' => '1')) }}
							</div>
							<br>
							{{ Form::label('lifetimeinseconds', 'HIT Lifetime (seconds)', 
								array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-2">
								{{ Form::input('number','lifetimeinseconds',  $crowdtask->lifetimeInSeconds, 
									array('class' => 'form-control input-sm', 'min' => '1')) }}
							</div>
							<br>
							{{ Form::label('autoapprovaldelayinseconds', 'Auto approval delay (seconds)', 
								array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-2">
							{{ Form::input('number','autoapprovaldelayinseconds',  $crowdtask->autoApprovalDelayInSeconds, 
								array('class' => 'form-control input-sm', 'placeholder' => '1 day = 86400', 'min' => '1')) }}
							</div>
							<br>
							{{ Form::label('reward', 'Reward', array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-2">
							<span class="input-group-addon">$</span> 
							{{ Form::input('number', 'reward',  $crowdtask->reward['Amount'], array('class' => 'form-control input-sm', 'min' => '0.01', 'step' => '0.01')) }}
							</div>
							<br>	
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
</div>

@stop