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
								{{ Form::label('maxAssignments', 'Max assignments', 
								array('class' => 'col-xs-4 control-label')) }}

							<div class="input-group col-xs-2">
								{{ Form::input('number', 'maxAssignments',  null, 
									array('class' => 'form-control input-sm col-xs-6', 'min' => '1')) }}
							</div>
							<br>
							{{ Form::label('assignmentDur', 'Assignment duration (seconds)', 
								array('class' => 'col-xs-4 control-label')) }}

							<div class="input-group col-xs-2">
								{{ Form::input('number', 'assignmentDur',  null, 
									array('class' => 'form-control input-sm', 'min' => '1')) }}
							</div>

							<br>
							{{ Form::label('reward', 'Reward', array('class' => 'col-xs-4 control-label')) }}
							<div class="input-group col-xs-2">
							<span class="input-group-addon">$</span> 
							{{ Form::input('number', 'reward',  null, array('class' => 'form-control input-sm', 'min' => '0.01', 'step' => '0.01')) }}
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