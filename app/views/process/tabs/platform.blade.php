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
						  <label class="btn btn-primary active">
						    {{ Form::checkbox('amt', 'false', false, array('id' => 'amt-button') )}} Mechanical Turk
						  </label>
						  <label class="btn btn-primary">
						   	{{ Form::checkbox('cf', 'false', false)}} Crowdflower
						  </label>
						</div>
						<div id="amt-div">
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