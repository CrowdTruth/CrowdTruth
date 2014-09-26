@extends('layouts.default_new')
@section('content')
@include('layouts.flashdata')
<style>
.panel-body {
	overflow-x:visible;
}
.row {
	margin-top: 20px;
}
</style>
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
							
							<div class="row">
							{{ Form::label('title', 'Select a title', array('class' => 'col-xs-4 control-label')) }}
								<div class="col-xs-6">


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
									    	if ( $pos > 0) {
									    		$t = trim(substr($value, 0, $pos));
									    		if(!array_key_exists($t, $aTitles))
										    		$aTitles[$t] = $t;
									    	}
										}

										$_aTypes = \MongoDB\Entity::where("documentType", "job")->where("format", $_format)->distinct('type')->get();
									    $_aTypes = array_flatten($_aTypes->toArray());
									    foreach($_aTypes as $key=>$value){
									    	if(!isset($aTypes[$value]))
										    	$aTypes[$value] = $value;
										}
										

										if($phpres = Session::get('templatetype')){
											if(!isset($aTypes[$phpres]))
												$phpres =null;
										}

										if($phprest = Session::get('title')){
											$pos = strpos($phprest, '[[');
									    	if ( $pos > 0)
												$phprest = trim(substr($phprest, 0, $pos));
											
											if(!isset($aTitles[$phprest]))
												$phprest =null;
										}
										
									?>

									{{ Form::select('title',  $aTitles, $phprest, array('class' => 'selectpicker', 'data-toggle'=> 'tooltip')) }}
								</div>
								
								
								<div class="col-xs-6 col-xs-offset-4">
									{{ Form::text('titleOwn', null, array('class' => 'form-control col-xs-6', 'placeholder' => 'Create new title')) }}
								</div>
							</div>
								
							<div class="row">
							{{ Form::label('templateType', 'Select a template-type', 
									array('class' => 'col-xs-4 control-label')) }}
								<div class="col-xs-6">
									{{ Form::select('templateType',  $aTypes, $phpres, array('class' => 'selectpicker', 'data-toggle'=> 'tooltip', 'templateType'=>'')) }}		
								</div>
								<div class="col-xs-6 col-xs-offset-4">
									{{ Form::text('templateTypeOwn', null, array('class' => 'form-control col-xs-4', 'placeholder' => 'Create new template')) }}
								</div>
							
							</div>
							<div class="row">
							{{ Form::label('description', 'Describe the job with keywords', 
									array('class' => 'col-xs-4 control-label')) }}
								<div class="col-xs-6">
									{{ Form::text('description', null, array('class' => 'form-control col-xs-4', 'placeholder' => 'Keywords')) }}
								</div>
							</div>
							
							<div class="row">
								<div class="col-xs-10">
									{{ Form::submit('Create Job', array('class' => 'btn btn-primary pull-right')); }}
								</div>
							</div>
						{{ Form::close()}}


					</div>
				</div>
			</div>	
		</div>
	</div>
</div>		
@endsection
