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

					@if (isset($status_upload['error']))
						<div class="panel panel-danger">
							<div class="panel-heading">
								<h4><i class="fa fa-exclamation-triangle fa-fw"></i>Error</h4>
							</div>
							<div class="panel-body CW_messages">
								<ul class="list-group">
						@foreach ($status_upload['error'] as $status_message)
							<li class="list-group-item"><span class='message'> {{ $status_message }} </li>
						@endforeach
								</ul>
							</div>
						</div>
					@endif

					@if(isset($status_upload['success']))
						<div class="panel panel-success">
							<div class="panel-heading">
								<h4><i class="fa fa-check fa-fw"></i>Success</h4>
							</div>
							<div class="panel-body CW_messages">
								<ul class="list-group">
						@foreach ($status_upload['success'] as $status_message)
							<li class="list-group-item"><span class='message'> {{ $status_message }} </li>
						@endforeach
								</ul>
							</div>
						</div>
					@endif

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4><i class="fa fa-upload fa-fw"></i>Select CSV result file</h4>
							</div>
							<div class="panel-body">							

								{{ Form::open(array('action' => 'MediaController@postImportresults', 'files' => 'true')) }}
								<div class="form-horizontal">
								
									<div class="form-group">
										<label for="category" class="col-sm-3 control-label">Choose File(s)</label>
										<div class="col-sm-6">
											<input type="file" name="files[]" class="btn uploadInput" multiple />
											<!-- <p class='uploadHelpText'>Allowed filetypes are: txt</p> -->
										</div>
									</div>
								
									<div class="form-group">
										<label for="domain_type" class="col-sm-3 control-label">Type of Results</label>
										<div class="col-sm-5">
											@if(isset($mainSearchFilters['media']['categories']))
												<select name="documentType" data-query-key="match[documentType]" class="documentType selectpicker pull-left show-tick" data-selected-text-format="count>3" title="Choose Document-Type(s)" data-width="auto" data-show-subtext="true">
													<option value="new" class="select_new">New Type</option>
													@foreach($mainSearchFilters['media']['categories'] as $project => $documentTypes)
														<optgroup label="{{ $project }}">
															@foreach($documentTypes as $key => $doctype)
																<option value="{{ $key }}" class="select_{{ $key }}" data-subtext="{{ $doctype['count'] }} Items">{{ $doctype['label'] }}</option>
															@endforeach
														</optgroup>
													@endforeach
												</select>
											@endif
										</div>
									</div>
									
									<div class="form-group">
										<label for="domain_type" class="col-sm-3 control-label">Project</label>
										<div class="col-sm-5">
											<select name="documentType" data-query-key="match[documentType]" class="documentType selectpicker pull-left show-tick" data-selected-text-format="count>3" title="Choose Document-Type(s)" data-width="auto" data-show-subtext="true">
												@foreach($projects as $project)
													<option value="{{ $project }}" class="select_{{ $project }}">{{ $project }}</option>
												@endforeach
											</select>	
										</div>
									</div>
	
									<div class="form-group" id="button">
										<div class="col-sm-offset-3 col-sm-5">
										{{ Form::button('Submit', array('type' => 'submit', 'value' => 'onlinedata', 'class' => 'btn btn-info')) }} 										
										</div>
									</div>
									
								</div>
								{{ Form::close() }}
							</div>
						</div>
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