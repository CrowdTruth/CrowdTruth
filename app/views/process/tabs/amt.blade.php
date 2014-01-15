@extends('layouts.default')
<!--
Todo:
	- Required fields
	- Model
	- Form route
	- Optional: dropdown http://getbootstrap.com/components/#input-groups-buttons-dropdowns

-->


@section('content')
				

				<div class="col-xs-10 col-md-offset-1">
					<div class='maincolumn CW_box_style'>

						<div class='tab'>
@include('process.nav')
@include('layouts.flashdata')
									
						</div>
						<div>
							<div class="panel panel-default">		
	<div class="panel-heading">
		<h4>Create your AMT-job</h4>
	</div>
	<div class="panel-body">
<!--		{{ Form::open(array('class' => 'form-horizontal', 'action' => 'ProcessController@postSubmit', 'method' => 'POST')) }} -->
{{ Form::model($crowdtask, array('class' => 'form-horizontal crowdtask', 'action' => 'ProcessController@postSubmit', 'method' => 'POST'))}}
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
		</fieldset>
		<br>
		<br>
		<fieldset>
			<legend>Question</legend>
		</fieldset>
		<br>
		<br>
		<fieldset>
			<legend>Parameters</legend>
			{{ Form::label('csvfilename', 'CSV serverfile', array('class' => 'col-xs-4 control-label')) }}
			<div class="input-group">
			 	{{ Form::text('csvfilename', '', array('class' => 'form-control')) }}
			 	<span class="input-group-btn">
			 		{{ Form::button('Browse...', array('class' => 'btn btn-default')) }}
			 	</span>
			</div>
			<br>
			<span class="col-xs-4"></span><span class="col-xs-8">-- Or, to create a single HIT, provide the parameters here. --</span>
			<br>
			{{ Form::label('params[]', 'Mock parameter 1', array('class' => 'col-xs-4 control-label')) }}
			<div class="input-group col-xs-8">
				{{ Form::text('params[]', '', array('class' => 'form-control')) }}
			</div>	
		</fieldset>
		<br>
		<br>
		<fieldset>
			<legend>Job details</legend>

			{{ Form::label('maxassignments', 'Max assignments', 
				array('class' => 'col-xs-4 control-label')) }}

			<div class="input-group col-xs-2">
				{{ Form::input('number', 'maxassignments',  $hit->getMaxAssignments(), 
					array('class' => 'form-control input-sm col-xs-6', 'min' => '1')) }}
			</div>

			{{ Form::label('assignmentdurationinseconds', 'Assignment duration (seconds)', 
				array('class' => 'col-xs-4 control-label')) }}

			<div class="input-group col-xs-2">
				{{ Form::input('number', 'assignmentdurationinseconds',  $hit->getAssignmentDurationInSeconds(), 
					array('class' => 'form-control input-sm', 'min' => '1')) }}
			</div>

			{{ Form::label('lifetimeinseconds', 'HIT Lifetime (seconds)', 
				array('class' => 'col-xs-4 control-label')) }}
			<div class="input-group col-xs-2">
				{{ Form::input('number','lifetimeinseconds',  $hit->getLifetimeInSeconds(), 
					array('class' => 'form-control input-sm', 'min' => '1')) }}
			</div>

			{{ Form::label('autoapprovaldelayinseconds', 'Auto approval delay (seconds)', 
				array('class' => 'col-xs-4 control-label')) }}
			<div class="input-group col-xs-2">
			{{ Form::input('number','autoapprovaldelayinseconds',  $hit->getAutoApprovalDelayInSeconds(), 
				array('class' => 'form-control input-sm', 'placeholder' => '1 day = 86400', 'min' => '1')) }}
			</div>
		</fieldset>
		<br>
		<br>
		<fieldset>
			<legend>Payment details:</legend>
			{{ Form::label('reward', 'Reward', 
				array('class' => 'col-xs-4 control-label')) }}
			<div class="input-group col-xs-2">
			<span class="input-group-addon">$</span> 
			{{ Form::input('number', 'reward',  $hit->getReward()['Amount'], 
				array('class' => 'form-control input-sm', 'min' => '0.01', 'step' => '0.01')) }}
			</div>
		</fieldset>
		<br>
		<br>
		<fieldset>
			<legend>Qualification Requirements</legend>
			<?php	$types = array(	'000000000000000000L0' => 'Assignments Approved (%)',
									'00000000000000000040' => 'Number of HITs Approved',
									'00000000000000000071' => 'Locale',
									'00000000000000000060' => 'Adult'		
							); ?>
			<div class="form-group">
			@foreach($types as $key=>$val)	
			<?php 
				// standard values.
				$c = false; $o = 'EqualTo'; $t = '';
				if($val=='Locale' || $val == 'Adult') $o = 'EqualTo';
				if($val=='Adult') $t = '1';

				if($hit->getQualificationRequirement()){
						foreach($hit->getQualificationRequirement() as $q){
							if($q['QualificationTypeId'] == $key){
								$c = true;
								if(isset($q['Comparator'])) 		$o = $q['Comparator'];
								if(isset($q['IntegerValue']))		$t = $q['IntegerValue'];
								if(isset($q['LocaleValue'])) 		$t = $q['LocaleValue'];
								if(isset($q['RequiredToPreview']))	$x = $q['RequiredToPreview'];

							} 
						}
					}
				?>

				<div class="col-sm-4">
				{{ Form::checkbox("qr[$key][checked]", 'true', $c, array('id' => $key)) }}
				{{ Form::label($key, $val) }}

				</div>
				<div class="input-group col-sm-4">	
				{{ Form::select("qr[$key][comparator]", array(
					'LessThan' => '<',
					'LessThanOrEqualTo' => '<=',
					'GreaterThan' => '>',
					'GreaterThanOrEqualTo' => '>=',
					'EqualTo' => '==',
					'NotEqualTo' => '!=',
					'Exists' => '&exist;'
				), $o, array('style' => 'margin-right:10px; line-height:29px; height:29px')) }}

				{{ Form::text("qr[$key][value]", $t, array('class' => 'form-control input-sm', 'style' => 'width:100px')) }}
				</div>		
			@endforeach				
			</div>
		</fieldset>
		<br>
		<br>
		<fieldset>
			<legend>Assignment Review Policy</legend>
		</fieldset>
		<label>AnswerKey</label><br>
		<?php
				$arp = $hit->getAssignmentReviewPolicy();
				foreach($questionids as $qid){
					$val = '';		
					if($arp)
						foreach($arp['AnswerKey'] as $q=>$v)
							if($q == $qid) $val = $v;

					echo "<label class='col-xs-4'>$qid</label><div class='input-group col-xs-4'><input name='answerkey[$qid]' value='$val' class='form-control input-sm'/></div>";

				}
			?>


		<br>
		<br>
		<label>Parameters</label><br>
		<?php
				$types = array( 'ApproveIfKnownAnswerScoreIsAtLeast', 'ApproveReason', 'RejectIfKnownAnswerScoreIsLessThan', 
								'RejectReason', 'ExtendIfKnownAnswerScoreIsLessThan', 
								'ExtendMaximumAssignments', 'ExtendMinimumTimeInSeconds'); ?>
		@foreach($types as $type)
			<span class='col-sm-5'>
				{{ Form::checkbox("arp[$type][checked]", 'true', false, array('id' => $type)) }}
				{{ Form::label($type, $type) }}
			</span>

				{{ Form::text("arp[$type][0]", '', array('class' => 'col-sm-4')) }}

			<br><br>
		@endforeach
		<br>
		{{ Form::hidden("template", $template) }}
		{{ Form::submit('Submit', array('class' => 'btn btn-lg btn-primary pull-right')); }}
		{{ Form::close() }}
	</div>	
</div>
						</div>	
					</div>
					
				</div>
				
@stop

