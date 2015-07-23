@extends('layouts.default_new')

@section('head')
{{ stylesheet_link_tag('bootstrap-select.css') }}
{{ stylesheet_link_tag('bootstrap-dropdown-checkbox.css') }}
{{ javascript_include_tag('jquery-1.10.2.min.js') }}
@stop

@section('content')
@section('pageHeader', 'Upload Media')

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

						{{ Form::open(array('action' => 'MediaController@postUpload', 'files' => 'true')) }}
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4><i class="fa fa-file-text-o fa-fw"></i> Input file</h4>
							</div>
							<div class="panel-body">							
								<div class="form-horizontal">

									<div class="form-group">
										<label for="category" class="col-sm-3 control-label">Choose File(s)</label>
										<div class="col-sm-6">
											<input type="file" name="files[]" class="btn uploadInput" multiple />
										</div>
									</div>
								
									<div class="form-group">
										<label for="category" class="col-sm-3 control-label">Project:</label>
										<div class="col-sm-5">
										@if(count($projects)==1)
											{{ $projects[0] }}
											{{ Form::hidden('projectname', $projects[0]) }}
										@else
											<select name="projectname" class="selectpicker" id="project" title="Project">
												<option data-hidden="true"></option>
											@foreach($projects as $project)
												<option value="{{ $project }}">{{ $project }}</option>
											@endforeach
											</select>
										@endif
										</div>
									</div>
								</div>						
							</div>
							<div class='panel-footer'>
								{{ Form::button('Upload Media', array('type' => 'submit', 'value' => 'upload', 'class' => 'btn btn-primary')) }} 										
							</div>	
						</div>
						{{ Form::close() }}		

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4><i class="fa fa-upload fa-fw"></i>Online Sources</h4>
							</div>
							<div class="panel-body">
								{{ Form::open(array('action' => 'MediaController@postOnlinedata', 'class' => 'onlineForm')) }}
								<div class="form-horizontal">
									<div class="form-group">
										<label for="source_name" class="col-sm-3 control-label">Source Name</label>
										<div class="col-sm-5">
											<select name="source_name" class="form-control toggle-data" id="source_name">
												<option value="">--</option>
												<option value="source_beeldengeluid" data-toggle="source_name">Netherlands Institute for Sound and Vision</option>
												<option value="source_rijksmuseum" data-toggle="source_name">Rijksmuseum ImageGetter</option>
											</select>
										</div>
									</div>
									<div class="form-group is_source_beeldengeluid hidden" id="inputvideo">
										<label for="number" class="col-sm-3 control-label" class="numberVideos">Number of videos:</label>
										<div class="col-sm-6">
											<input type="number" name="numberVideos" min="0" />
										</div>
									</div>

									<div class="form-group hidden" id="button">
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
	{{ javascript_include_tag('jquery.chained.min.js') }}
	{{ javascript_include_tag('bootstrap-select.js') }}

	<script type="text/javascript">
		$(document).ready(function () {
		
			$('.selectpicker').selectpicker({
				iconBase: 'fa',
				tickIcon: 'fa-check'
			});

		
			$('button[value=onlinedata]').on('click', function() {
				if($('select#source_name').val() == "source_beeldengeluid")
				{
					$('.onlineForm').submit(function(event) {

						var formData = {
							'source_name' 			: "source_beeldengeluid",
							'numberVideos' 			: $('.onlineForm input[name=numberVideos]').val()
						};

						// process the form
						$.ajax({
							type 		: 'POST', // define the type of HTTP verb we want to use (POST for our form)
							url 		: $('.onlineForm').attr('action'), // the url where we want to POST
							data 		: formData, // our data object
							dataType 	: 'json', // what type of data do we expect back from the server
							encode          : true
						})
						// using the done promise callback
						.done(function(data) {
							// log data to the console so we can see
							console.log(data); 
								// here we will handle errors and validation messages
						});

						setTimeout( function(){ 
							location.href = "{{ URL::to('media/upload') }}"; }, 2000);

						// stop the form from submitting the normal way and refreshing the page
						event.preventDefault();
					});					
				}
			});
	
			$('.toggle-data').on('change', function() {
				var toggle = $(".toggle-data option:selected").val();
				var optionDiv = $(".is_" + toggle);
				optionDiv.removeClass("hidden");

				var button = $("#button");
				if( $(".toggle-data option:selected").val() != ""){
					button.removeClass("hidden");
				} else {
					button.addClass("hidden");
				}

				var inputvideo = $("#inputvideo");
				if ($('.toggle-data option:selected').val() != "source_beeldengeluid") {
					inputvideo.addClass("hidden");
				}
			});

			$("#domain_type").on('change', function() {
				var type = $(this).val();
				if(type=="domain_type_other") {
					$("#domain_other_div").removeClass("hidden");
				} else {
					$("#domain_other_div").addClass("hidden");
				}
			});

			$("#document_type").on('change', function() {
				var type = $(this).val();
				if(type=="document_type_other") {
					$("#document_other_div").show();
				} else {
					$("#document_other_div").hide();
				}
			});
		});
	</script>
@stop
