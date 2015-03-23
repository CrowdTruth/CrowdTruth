@extends('layouts.default_new')

@section('head')
{{ stylesheet_link_tag('bootstrap-select.css') }}
{{ stylesheet_link_tag('bootstrap-dropdown-checkbox.css') }}
{{ javascript_include_tag('jquery-1.10.2.min.js') }}
@stop

@section('content')
@section('pageHeader', 'Import Results')

				<!-- START upload_content --> 
				<div class="col-xs-10 col-sm-offset-1">
					<div class='maincolumn CW_box_style'>
	@include('layouts.flashdata')
	@include('media.layouts.nav_new')	
				
					{{ Form::open(array('action' => 'MediaController@postImportresults', 'files' => 'true', 'enctype' => 'multipart/form-data')) }}
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4><i class="fa fa-file-text-o fa-fw"></i> Input file</h4>
							</div>
							<div class="panel-body">							
								<div class="form-horizontal">
							
									<div class="form-group">
										<div class='col-xs-12'>On this page you can add crowdsourcing results from a csv file. This will recreate the original input data, crowdsourcing job and output data as if it was ran on CrowdTruth. Additionally, the result file is saved in the system so that it can be re-used.</div>
									</div>
							
									<div class="form-group">
										<label for="category" class="col-sm-3 control-label">Choose File(s)</label>
										<div class="col-sm-6">
											<input type="file" name="file" class="btn uploadInput" />
											<p class='help-block'>Select a CSV file with the full results of an AMT or CrowdFlower job.</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4><i class="fa fa-sign-in fa-fw"></i> Input Data</h4>
							</div>
							<div class="panel-body">							
								<div class="form-horizontal">
								
									<div class="form-group">
										<div class='col-xs-12'>Select the type of the original input of the job that is included in the result file. If the original input data does not yet exist in the platform, it will be added.</div>
									</div>
								
									<div class="form-group">
										<label for="domain_type" class="col-sm-3 control-label">Input Type</label>
										<div class="col-sm-9">
											@if(isset($mainSearchFilters['media']['categories']))
												<select name="documentType" data-query-key="match[documentType]" class="documentType selectpicker pull-left show-tick" data-selected-text-format="count>3" title="Choose Document-Type(s)" data-width="auto" data-show-subtext="true">
													@foreach($mainSearchFilters['media']['categories'] as $project => $documentTypes)
														<optgroup label="{{ $project }}">
															@foreach($documentTypes as $key => $doctype)
																<option value="{{ $project }}-{{ $key }}" class="select_{{ $key }}" data-subtext="{{ $doctype['count'] }} Items">{{ $doctype['label'] }}</option>
															@endforeach
														</optgroup>
													@endforeach
												</select>
											@endif
										</div>
									</div>
									
									<div class="form-group">
										<label for="domain_type" class="col-sm-3 control-label">Custom</label>
										<div class='col-sm-3'>
											<input type='text' class='form-control' name='input-project' placeholder='Project' />
										</div>
										<div class='col-sm-3'>
											<input type='text' class='form-control' name='input-type' placeholder='Type' />
										</div>
									</div>
								</div>
							</div>
						</div>
									
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4><i class="fa fa-sign-out fa-fw"></i> Output Data</h4>
							</div>
							<div class="panel-body">							
								<div class="form-horizontal">
								
									<div class="form-group">
										<div class='col-xs-12'>Select the type of the results, this will decide the answer vector on which the CrowdTruth metrics are used. For both the type of input and results, you can either select an existing document type, or create a new one. Though, both the input and results must be saved into the same project.</div>
									</div>
									
									<div class="form-group">
										<label for="domain_type" class="col-sm-3 control-label">Output Type</label>
										<div class="col-sm-5">
											@if(isset($mainSearchFilters['media']['categories']))
												<select disabled name="documentType" data-query-key="match[documentType]" class="documentType selectpicker pull-left show-tick" data-selected-text-format="count>3" title="Choose Document-Type(s)" data-width="auto" data-show-subtext="true">
													<option value="sound" class="select_sound" data-subtext="">Disabled</option>
													@foreach($mainSearchFilters['media']['categories'] as $project => $documentTypes)
														<optgroup label="{{ $project }}">
															@foreach($documentTypes as $key => $doctype)
																{{--<option value="{{ $key }}" class="select_{{ $key }}" data-subtext="{{ $doctype['count'] }} Items">{{ $doctype['label'] }}</option>--}}
															@endforeach
														</optgroup>
													@endforeach
												</select>
											@endif

										</div>
									</div>								
								</div>
							</div>
							<div class='panel-footer'>
								{{ Form::button('Add results', array('type' => 'submit', 'value' => 'onlinedata', 'class' => 'btn btn-primary')) }} 										
							</div>
						</div>
					{{ Form::close() }}
					</div>
				</div>
				<!-- STOP upload_content --> 				
@stop

@section('end_javascript')
{{ javascript_include_tag('generalsearch_manifest') }}
<script>
$('document').ready(function(){

	$('.selectpicker').selectpicker({
		iconBase: 'fa',
		tickIcon: 'fa-check'
	});

});

</script>
@stop