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
							{{ Form::label('title', 'Select a Title or give your own', 
									array('class' => 'col-xs-12 col-sm-4 control-label')) }}

								<div class="col-xs-12 col-sm-3">
									<?php 
										// Get a list of titles and template types which are already in the database
										// and put them to dropdown

										$aTitles = array(null => '---');
										$aTypes = array(null => '---');
										$_format = (unserialize(Session::get('batch'))->format);
										$batchUnits = (unserialize(Session::get('batch'))->parents);


										$batchUnitContent = Entity::where("_id", $batchUnits[0])->get()->first();

										$unitAttributes = array();
										$c = array_change_key_case(array_dot($batchUnitContent['content']), CASE_LOWER);
										
										foreach($c as $key => $val){
											$key = strtolower(str_replace('.', '_', $key));
											$unitAttributes[$key] = $key;
										}
									//	dd($unitAttributes);

										$_aTitles = Entity::where("type", "jobconf")->where("format", $_format)->distinct("content.title")->get();
									    $_aTitles = array_flatten($_aTitles->toArray());	
								    
									    foreach($_aTitles as $key=>$value){
									    	$pos = strpos($value, '[[');
									    	if ( $pos > 0) {
									    		$t = trim(substr($value, 0, $pos));
									    		if(!array_key_exists($t, $aTitles))
										    		$aTitles[$t] = $t;
									    	}
										}

										$_aTypes = Template::distinct('type')->get();
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
										
										$colNameString = implode(",", $unitAttributes);
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
						<div class='row' style='margin-top:40px;'>
							{{ Form::label('platform', 'Platform', 
									array('class' => 'col-xs-12 col-sm-4 control-label')) }}
								<div class="col-xs-12 col-sm-3">
									{{ Form::select('platform',  array('cf' => 'CrowdFlower', 'game' => 'BioCrowd'), 'cf', array('class' => 'selectpicker', 'id' => 'platform', 'data-width' => '100%', 'data-container' => 'body', 'data-toggle'=> 'tooltip')) }}
								</div>
							
						</div>
						<div class='row' style='margin-top:40px; margin-bottom: 20px;'>
							{{ Form::label('templateType', 'Select a Template or define a new one', array('class' => 'col-xs-12 col-sm-4 control-label')) }}
								<div class="col-xs-12 col-sm-3">
									{{ Form::select('templateType',  $aTypes, $phpres, array('class' => 'selectpicker', 'id' => 'chosenTempType', 'data-width' => '100%', 'data-container' => 'body', 'data-toggle'=> 'tooltip', 'templateType'=>'')) }}
								</div>
								<div class="col-xs-12 col-sm-4">
									{{ Form::text('templateTypeOwn', null, array('class' => 'form-control', 'id' => 'typeNewType', 'placeholder' => 'New Template')) }}
								</div>
						</div>
						<div class='row hidden' id="placeholderReqColumns" style='margin-top:40px; margin-bottom: 20px;'>
							{{ Form::label('batchColumns', 'Select the columns that you want to use in the new template', array('class' => 'col-xs-12 col-sm-4 control-label')) }}
							{{ Form::hidden('newcolnames', '', array('class' => 'form-control', 'id' => 'newcolnames')) }}	
								<div class="col-xs-12 col-sm-3">
									{{ Form::select('batchColumns[]',  $unitAttributes, null, array('class' => 'selectpicker', 'id' => 'multiselBatchCol', 'data-width' => '100%', 'data-container' => 'body', 'data-toggle'=> 'tooltip', 'multiple' => 'multiple', 'batchColumns[]'=>'') ) }}
								</div>
								<div class="col-xs-2">
									{{ Form::button('Select All', array('id' => 'selectAll', 'data-width' => '100%', 'data-container' => 'body', 'data-toggle'=> 'tooltip') ) }}
								</div>
								<div class="col-xs-2">
									{{ Form::button('Deselect All',  array('id' => 'deselectAll', 'data-width' => '100%', 'data-container' => 'body', 'data-toggle'=> 'tooltip') ) }}
								</div>
								<div class="col-md-6 col-md-6" id="placeholderNewNames">
								</div>
						</div>
						<div class='row hidden' id="placeholderAssociateColumns" style='margin-top:40px; margin-bottom: 20px;'>
							{{ Form::label('templateFields', 'Associate each template field with batch columns', array('class' => 'col-md-4 col-md-4 col-md-4 control-label')) }}
							{{ Form::hidden('bcol', $colNameString, array('class' => 'form-control', 'id' => 'bcol')) }}
							<div class="col-md-4 col-md-4" id="assocColumns">
							</div>
						</div>
						<div class='row hidden' id="placeholderExtraColumns" style='margin-top:40px; margin-bottom: 20px;'>
							{{ Form::label('moreColumns', 'Add more columns?', array('class' => 'col-xs-12 col-sm-4 control-label')) }}
							<div class="col-md-4 col-md-4">
								{{ Form::select('addMoreColumns[]',  $unitAttributes, null, array('class' => 'selectpicker', 'id' => 'addMoreColumns', 'data-width' => '100%', 'data-container' => 'body', 'data-toggle'=> 'tooltip', 'multiple' => 'multiple', 'addMoreColumns[]'=>'') ) }}
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
@stop
