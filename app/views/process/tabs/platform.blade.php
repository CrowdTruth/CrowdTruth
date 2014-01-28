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
						<h4>Choose platform-specific options</h4>
					</div>
					<div class="panel-body">
						{{ Form::model($crowdtask, array('class' => 'form-horizontal crowdtask', 'action' => array('ProcessController@postFormPart', 'submit'), 'method' => 'POST'))}}
						<div data-toggle="buttons">
							<label>Select the platform you want to send your job to:</label>	
						  	<label class="btn btn-primary">
						   		{{ Form::checkbox('cf', 'false', false, array('id' => 'cf-button') )}} Crowdflower
						  	</label>
						  	<label class="btn btn-primary">
						    	{{ Form::checkbox('amt', 'false', false, array('id' => 'amt-button') )}} Mechanical Turk
						  	</label>
						</div>
						<div id="cf-div" style="padding: 10px;">
							<fieldset>
								<legend>CrowdFlower</legend> 
								{{ Form::label('judgmentsPerWorker', 'Maximal judgments per worker', array('class'=>'col-xs-4 control-label')) }}
								<div class="input-group col-xs-2">
									{{ Form::input('number', 'judgmentsPerWorker', null, array('class'=>'form-control input-sm', 'min' => '1')) }}
								</div>
							</fieldset><br>	
						</div>
						<div id="amt-div" style="padding: 10px;">
							<fieldset>
								<legend>AMT Duration</legend>
									{{ Form::label('hitLifetimeInMinutes', 'HIT Lifetime (minutes)', 
										array('class' => 'col-xs-4 control-label')) }}
									<div class="input-group col-xs-2">
										{{ Form::input('number','hitLifetimeInMinutes',  null, 
											array('class' => 'form-control input-sm', 'min' => '1')) }}
									</div>
									<br>
									{{ Form::label('autoApprovaldelayInMinutes', 'Auto approval delay (minutes)', 
										array('class' => 'col-xs-4 control-label')) }}
									<div class="input-group col-xs-2">
									{{ Form::input('number','autoApprovaldelayInMinutes',  null, 
										array('class' => 'form-control input-sm', 'placeholder' => '1 day = 1440', 'min' => '1')) }}
									</div>
								</fieldset>
								<br>
								<br>
							<fieldset>
								<legend>AMT Misc.</legend>
								{{ Form::label('requesterAnnotation', 'Requester Annotation', 
									array('class' => 'col-xs-4 control-label')) }}
								<div class="input-group col-xs-2">
									{{ Form::text('requesterAnnotation',  null, 
										array('class' => 'form-control input-sm')) }}
								</div>
							</fieldset>
							<br>
							<br>
							<fieldset>
								<legend>AMT Qualification Requirements</legend>
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
									if($crowdtask->qualificationRequirement){
											foreach($crowdtask->qualificationRequirement as $q){
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
							<fieldset>
								<legend>AMT Assignment Review Policy</legend>
							
							<label>AnswerKey</label><br>
							<?php  $arp = $crowdtask->assignmentReviewPolicy; ?>
							@if (count($csvfields)>0)
								{{ Form::select('answerfield', $csvfields)}}
							@else
							<?php
									foreach($questionids as $qid){
									 	$val = '';		
									 	if($arp)
									 		foreach($arp['AnswerKey'] as $q=>$v)
									 			if($q == $qid) $val = $v;

										echo "<label class='col-xs-4'>$qid</label><div class='input-group col-xs-4'><input name='answerkey[$qid]' value='$val' class='form-control input-sm'/></div>";
									 }
								?>
							@endif	
							<br>
							<br>
							<label>Parameters</label><br>
							<?php
									$types = array( 'ApproveIfKnownAnswerScoreIsAtLeast', 'ApproveReason', 'RejectIfKnownAnswerScoreIsLessThan', 'RejectReason', 'ExtendIfKnownAnswerScoreIsLessThan', 'ExtendMaximumAssignments', 'ExtendMinimumTimeInSeconds'); ?>
							@foreach($types as $type)
								@if(isset($arp['Parameters'][$type]))
									<?php $c = true; $val = $arp['Parameters'][$type]; ?>
								@else 
									<?php $c = false; $val = ''; ?>		
								@endif 
										<span class='col-sm-5'>
											{{ Form::checkbox("arp[$type][checked]", 'true', $c, array('id' => $type)) }}
											{{ Form::label($type, $type) }}
										</span>
											{{ Form::text("arp[$type][0]", $val, array('class' => 'col-sm-4')) }}
										<br><br>
							@endforeach
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
</div>		

@endsection

@stop