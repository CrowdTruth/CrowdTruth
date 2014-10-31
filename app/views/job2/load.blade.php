@extends('layouts.default_new')
@section('head')
{{ stylesheet_link_tag('bootstrap-select.css') }}
{{ stylesheet_link_tag('bootstrap-dropdown-checkbox.css') }}
{{ stylesheet_link_tag('bootstrap.datepicker3.css') }}
{{ stylesheet_link_tag('custom.css') }}
@endsection
@section('content')
@include('layouts.flashdata')

<div class="col-xs-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>			
		<div class='tab'>
			<div>
				<div class="panel panel-default">		
					<div class="panel-heading">
						<h4>Provide job and template details</h4>
					</div>
					<div class="panel-body">
						{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('JobsController2@postLoadt', 'sandbox'), 'method' => 'POST')) }}
						<fieldset>
							<?php 							
								$aTypes = array(null => '---');
								$_format = Session::get('format_t');
								$_aTypes = \MongoDB\Template::where("format", $_format)->distinct('type')->get();
							    $_aTypes = array_flatten($_aTypes->toArray());		

							    foreach($_aTypes as $key=>$value){
							    	if(!isset($aTypes[$value]) and $value !== "NONE")
								    	$aTypes[$value] = $value;
								}	
							?>				
						{{ Form::label('templateType', 'Select a template-type ', array('class' => 'col-xs-4 control-label')) }}	
						{{ Form::select('templateType',  $aTypes, null, array('class' => 'selectpicker',   'data-container' =>'body',  'data-toggle'=> 'tooltip', 'templateType'=>'')) }}									
						</fieldset>
						<br/><br/>
						{{ Form::submit('Load', array('class' => 'btn btn-lg btn-primary pull-right', 'style' => 'margin-right:20px')); }}
						{{ Form::close()}}				
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>		
@endsection

@section('end_javascript')
{{ javascript_include_tag('bootstrap-select.js') }}
<script>
$(document).ready(function() {
$('.selectpicker').selectpicker();
});
</script>
}
@endsection
