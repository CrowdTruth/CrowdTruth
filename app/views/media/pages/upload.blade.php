@extends('layouts.default_new')

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

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4><i class="fa fa-upload fa-fw"></i>File input</h4>
							</div>
							<div class="panel-body">							

								{{ Form::open(array('action' => 'MediaController@postUpload', 'files' => 'true')) }}
								<div class="form-horizontal">

									<div class="form-group">
										<label for="file_format" class="col-sm-3 control-label">Type of File</label>
										<div class="col-sm-5">
											<select name="file_format" class="form-control" id="file_format">
												<option value="">--</option>
												<option value="file_format_text">Text</option>
												<option value="file_format_image">Image</option>
												<option value="file_format_video">Video</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="domain_type" class="col-sm-3 control-label">Domain</label>
										<div class="col-sm-5">
											<select name="domain_type" class="form-control" id="domain_type">
												<option value="">--</option>
												<option value="domain_type_art" class="file_format_image">Art</option>
												<option value="domain_type_medical" class="file_format_text">Medical</option>
												<option value="domain_type_news" class="file_format_text">News</option>
												<option value="domain_type_other" class="file_format_text">Other</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="document_type" class="col-sm-3 control-label">Type of Document</label>
										<div class="col-sm-5">
											<select name="document_type" class="form-control" id="document_type">
												<option value="">--</option>
												<option value="document_type_relex" class="domain_type_medical">RElex</option>
												<option value="document_type_csvresult" class="domain_type_medical">CSVResult</option>
												<option value="document_type_article" class="domain_type_medical domain_type_news">Article</option>
												<option value="document_type_book" class="domain_type_other">Book</option>
												<option value="document_type_painting" class="domain_type_art">Painting</option>
												<option value="document_type_drawing" class="domain_type_art">Drawing</option>
												<option value="document_type_picture" class="domain_type_art">Picture</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="category" class="col-sm-3 control-label">Choose File(s)</label>
										<div class="col-sm-6">
											<input type="file" name="files[]" class="btn uploadInput" multiple />
											<!-- <p class='uploadHelpText'>Allowed filetypes are: txt</p> -->
										</div>
									</div>

<!-- 									<div class="form-group">
										<label class="col-sm-3 control-label">Increment Filename/URI if entry already exists</label>
										<div class="col-sm-6" style="line-height:40px;">
											<input type="checkbox" name="increment" value="true" />
										</div>
									</div> -->

									<div class="form-group">
										<div class="col-sm-offset-3 col-sm-5">
										{{ Form::button('Submit', array('type' => 'submit', 'value' => 'upload', 'class' => 'btn btn-info')) }} 
										
										</div>
									</div>
								</div>

								{{ Form::close() }}				

							</div>
						</div>
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
										   <!-- <option value="source_template" data-toggle="source_name">New online source</option> -->
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

	<script type="text/javascript">
		$(document).ready(function () {
			$("#domain_type").chainedTo("#file_format");
			$("#document_type").chainedTo("#domain_type");

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
					location.href = "{{ URL::to('media/upload') }}";						
							}, 2000);




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
		});


	</script>
@stop
