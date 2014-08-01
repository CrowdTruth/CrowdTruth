@extends('layouts.default')
@section('content')
@include('layouts.flashdata')
<div class="col-xs-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>
					
		<div class='tab'>
			@include('job2.nav')
			<div>
				<div class="panel panel-default">		
					<div class="panel-heading">
						<h4>Preview task and submit</h4>
					</div>
					<div class="panel-body">
						{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('JobsController2@postSubmitFinal', 'sandbox'), 'method' => 'POST')) }}
							<fieldset>
							{{ Form::label('title', 'Select a title from the set of predefined ones or give your own', 
									array('class' => 'col-xs-5 control-label')) }}
								<div class="input-group col-xs-3">


									<?php 
										// Get a list of titles and template types which are already in the database
										// and put them to dropdown
										$aTitles = array(null => '---');
										$aTypes = array(null => '---');
										$_format = (unserialize(Session::get('batch'))->format);
										$_aTitles = \MongoDB\Entity::where("documentType", "jobconf")->where("format", $_format)->distinct("content.title")->get();
									    $_aTitles = array_flatten($_aTitles->toArray());
									    
									    foreach($_aTitles as $key=>$value){
									    	$pos = strpos($value, '[[');
									    	if ( $pos > 0)
										    	$aTitles[$value] = substr($value, 0, $pos);
										}

										$_aTypes = \MongoDB\Entity::where("documentType", "job")->where("format", $_format)->distinct('type')->get();
									    $_aTypes = array_flatten($_aTypes->toArray());
									    foreach($_aTypes as $key=>$value){
										    $aTypes[$value] = $value;
										}
										

										if($phpres = Session::get('templatetype')){
											if(!isset($aTypes[$phpres]))
												$phpres =null;
										}
										
									?>

									{{ Form::select('title',  $aTitles, null, array('class' => 'selectpicker', 'data-toggle'=> 'tooltip', 'title'=>'')) }}
								</div><div class="input-group col-xs-3">
									{{ Form::text('titleOwn', null, array('class' => 'form-control col-xs-2')) }}
								</div>
							
								
							
							<br/><br/>
							{{ Form::label('templateType', 'Select a template-type from the set of predefined ones or give your own', 
									array('class' => 'col-xs-5 control-label')) }}
								<div class="input-group col-xs-3">
									{{ Form::select('templateType',  $aTypes, $phpres, array('class' => 'selectpicker', 'data-toggle'=> 'tooltip', 'templateType'=>'')) }}		
									</div><div class="input-group col-xs-3">
									{{ Form::text('templateTypeOwn', null, array('class' => 'form-control col-xs-2')) }}
								</div>
						
							<br/>

							<!-- <br/><br/>
							{{ Form::label('keywords', 'Describe the job using a few keywords, separated by \',\'', 
									array('class' => 'col-xs-5 control-label')) }}
								<div class="input-group col-xs-3">
									{{ Form::text('keywords', null, array('class' => 'form-control col-xs-2')) }}
								</div>
						
							<br/> -->

							</fieldset>
	

						{{ Form::submit('Create Job', array('class' => 'btn btn-lg btn-default pull-right', 'style' => 'margin-right:20px')); }}
						{{ Form::close()}}




					</div>
				</div>
			</div>	
		</div>
	</div>
</div>		
@endsection
