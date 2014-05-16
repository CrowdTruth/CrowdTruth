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
						{{ Form::model($jobconf, array('class' => 'form-horizontal jobconf', 'action' => array('ProcessController@postFormPart', 'nextplatform'), 'method' => 'POST'))}}
							<input type="hidden" name="platformid" value="amt" />
							<div id="amt-div" style="padding: 10px;">
							<fieldset>
								<legend>AMT Duration</legend>
									{{ Form::label('hitLifetimeInMinutes', 'HIT Lifetime (minutes)', 
										array('class' => 'col-xs-4 control-label', 'data-toggle'=> 'tooltip', 'title'=>'The HIT expires in this time, even if it\'s not finished')) }}
									<div class="input-group col-xs-2">
										{{ Form::input('number','hitLifetimeInMinutes',  null, 
											array('class' => 'form-control input-sm', 'min' => '1', 'placeholder' => '1 day = 1440')) }}
									</div>
									<br>
									{{ Form::label('autoApprovalDelayInMinutes', 'Auto approval delay (minutes)', 
										array('class' => 'col-xs-4 control-label', 'data-toggle'=> 'tooltip', 'title'=>'After the Assignment is finished, automatically approve after...')) }}
									<div class="input-group col-xs-2">
									{{ Form::input('number','autoApprovalDelayInMinutes',  null, 
										array('class' => 'form-control input-sm', 'placeholder' => '1 day = 1440', 'min' => '1')) }}
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
									if(isset($jobconf['qualificationRequirement'])){
											foreach($jobconf['qualificationRequirement'] as $q){
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
									), $o, array('style' => 'margin-right:10px; line-height:29px; height:29px; width:40px; float:left')) }}

									{{ Form::text("qr[$key][value]", $t, array('class' => 'form-control input-sm', 'style' => 'width:100px')) }}
									</div>		
								@endforeach				
								</div>
							</fieldset>
							<br>
							<br>
							<fieldset>
								<legend>AMT Assignment Review Policy</legend>

							<label>Actions to take with gold questions</label><br><br>
							@if(empty($jobconf['answerfields']))
								<b>Note: </b>Please specify the gold fields in the details tab first!<br><br>
							@else
								<?php
									$arp = $jobconf['assignmentReviewPolicy'];
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
												{{ Form::text("arp[$type][0]", $val, array('class' => 'col-sm-4 input-sm')) }}
											<br><br>
								@endforeach
							@endif
							</fieldset>
							<br>
							<br>
							<fieldset>
							<legend>AMT Misc.</legend>
								{{ Form::label('frameheight', 'Frameheight (px)', 
									array('class' => 'col-xs-4 control-label', 'data-toggle'=> 'tooltip', 'title'=>'The height of the frame the worker sees the assignment in.')) }}
								<div class="input-group col-xs-2">
									{{ Form::input('number','frameheight',  null, 
										array('class' => 'form-control input-sm', 'min' => '300')) }}
								</div>
							</fieldset>		
						</div>
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