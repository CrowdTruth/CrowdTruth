@extends('layouts.default_new')
@section('content')
@include('layouts.flashdata')
<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>
					
		<div class='tab'>
			@include('job2.nav')
			<div>
				{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('JobsController2@postSubmitFinal', 'sandbox'), 'method' => 'POST')) }}
				<div class="panel panel-default">		
					<div class="panel-heading">
						<h4>Provide Job and Template details</h4>
					</div>
					<div class="panel-body">
						<div class="row" style='margin-top:20px;'>
							{{ Form::label('title', 'Select a Title give your own', 
									array('class' => 'col-xs-12 col-sm-4 control-label')) }}

								<div class="col-xs-12 col-sm-3">
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

										$_aTypes = \MongoDB\Template::where("format", $_format)->distinct('type')->get();
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
									
									{{ Form::select('title',  $aTitles, $phprest, array('class' => 'selectpicker', 'data-width' => '100%', 'data-container' => 'body', 'data-toggle'=> 'tooltip', 'title'=>'')) }}
								</div>
								<div class="col-xs-12 col-sm-4">
									{{ Form::text('titleOwn', null, array('class' => 'form-control', 'placeholder' => 'New Title')) }}
								</div>
						</div>
						<div class='row' style='margin-top:40px;'>
							{{ Form::label('description', 'Describe the Job', 
									array('class' => 'col-xs-12 col-sm-4 control-label')) }}
								<div class="col-xs-12 col-sm-7">
									{{ Form::text('description', null, array('class' => 'form-control', 'placeholder' => 'Keywords')) }}
								</div>	
							
						</div>
						<div class='row' style='margin-top:40px; margin-bottom: 20px;'>
							{{ Form::label('templateType', 'Select a Template or define a new one', 
									array('class' => 'col-xs-12 col-sm-4 control-label')) }}
								<div class="col-xs-12 col-sm-3">
									{{ Form::select('templateType',  $aTypes, $phpres, array('class' => 'selectpicker', 'data-width' => '100%', 'data-container' => 'body', 'data-toggle'=> 'tooltip', 'templateType'=>'')) }}		
								</div>
								<div class="col-xs-12 col-sm-4">
									{{ Form::text('templateTypeOwn', null, array('class' => 'form-control', 'placeholder' => 'New Template')) }}
								</div>
						</div>
					</div>
					<div class="panel-footer">
						{{ Form::submit('Create Job', array('class' => 'btn btn-primary pull-right')); }}
						<div class='clearfix'></div>
					</div>
				</div>
				{{ Form::close()}}
			</div>	
		</div>
	</div>
</div>		
@endsection
