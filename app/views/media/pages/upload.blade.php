@extends('layouts.default_new')

@section('head')
{{ stylesheet_link_tag('bootstrap-select.css') }}
{{ stylesheet_link_tag('bootstrap-dropdown-checkbox.css') }}
{{ stylesheet_link_tag('bootstrap.datepicker3.css') }}
@stop

@section('content')
@section('pageHeader', 'Add Media')

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
								<h4 style='display:inline; line-height:30px;'><i class="fa fa-upload fa-fw"></i> File input</h4>
								<a href='/media/upload' class="btn btn-danger btn-sm pull-right">Reset form</a>
								<div class="clearfix"></div>
							</div>
							<div class="panel-body">							

								{{ Form::open(array('action' => 'MediaController@postUpload', 'files' => 'true')) }}
								<div class="form-horizontal">
								
									<div class="form-group">
										<label for="category" class="col-sm-3 control-label">Source</label>
										<div class="col-sm-9">
										<div class="btn-group">
											<label class="btn btn-default col-sm-4" id='file_upload' for="my-file-selector">
												<i class="fa fa-upload fa-fw"></i>
												<input type="file" id="my-file-selector" name="files[]" class="btn uploadInput hidden" multiple />
												Select File
											</label>
											<!-- <p class='uploadHelpText'>Allowed filetypes are: txt</p> -->
											<select name="online_source" class="selectpicker pull-left show-tick" id="online_source" data-container="body" data-style="btn-default">
												<option data-hidden="true" data-icon="fa-cloud-download">Select Online Source</option>
												<option value="source_beeldengeluid" data-icon="fa-video-camera">Netherlands Institute for Sound and Vision</option>
												<option value="source_rijksmuseum" data-icon="fa-image">Rijksmuseum ImageGetter</option>
											</select>
											</div>
										</div>							
									</div>

								{{ Form::open(array('action' => 'MediaController@postOnlinedata', 'class' => 'onlineForm')) }}
									
									<div class="form-group" style='display: none;' id="soundandvision">
										<label for="number" class="col-sm-3 control-label" class="numberVideos">Amount of Videos:</label>
										<div class="col-sm-2">
											<input type="number" name="numberVideos" min="0" class="form-control" placeholder="Number" />
										</div>
									</div>
									
								{{ Form::close() }}	

									<div class="form-group">
										<label for="document" class="col-sm-3 control-label">Load Configuration</label>
										<div class="col-sm-5">
											<select name="document" id="document" class="selectpicker pull-left show-tick" title="Select a Document" data-show-subtext="true" data-container="body">
											<option data-hidden="true"></option>
											@foreach($uniqueDomains as $domainKey => $domain)
												@if($domainKey <> 'opendomain')
													<optgroup label="{{ $domain }}">
												@endif
												@foreach($docTypeData as $formatKey => $format)
													@foreach($format['document_types'] as $docTypeKey => $docType)
														@if(array_key_exists($domainKey, $docType['domains']))
															<option format="{{ $formatKey }}" doctype="{{ $docTypeKey }}" value="{{ $formatKey }}/{{ $domainKey }}/{{ $docTypeKey }}" domain="{{ $domainKey }}" data-icon="fa {{ $format['icon'] }}" data-subtext="{{ $docType['domains'][$domainKey]['count'] }} Items">{{ $docType['label'] }}</option>
														@endif
													@endforeach
												@endforeach
												@if($domainKey <> 'opendomain')
													</optgroup>
												@endif
											@endforeach
											</select>
										</div>
									</div>

								</div>
							</div>
						</div>

						<div id='customize_configuration' class="panel panel-default">
							<div class="panel-heading">
								<h4><i class="fa fa-gears fa-fw"></i> Customize Configuration</h4>
							</div>
							<div class="panel-body">	

								<div class="form-horizontal">
									<div class="form-group">
										<label for="format" class="col-sm-3 control-label">File Format</label>
										<div class="col-sm-5">
											<select id="format" name="format" class="selectpicker pull-left show-tick" title="Select a Format" data-container="body">
												<option data-hidden="true"></option>
												<option value="text" data-icon="fa-newspaper-o"> Text</option>
												<option value="image" data-icon="fa-image" >Image</option>
												<option value="sound" data-icon="fa-music"> Sound</option>
												<option value="video" data-icon="fa-video-camera"> Video</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="doctype" class="col-sm-3 control-label">Document Type</label>
										<div class="col-sm-3">
											<select id="doctype" name="doctype" class="selectpicker pull-left show-tick" title="Select a Document Type" data-container="body">
												<option data-hidden="true"></option>
											@foreach($uniqueDocTypes as $doctypeKey => $doctype)
												<option value="{{ $doctypeKey }}">{{ $doctype }}</option>
											@endforeach
											</select>
										</div>

										<div class="col-sm-4">
											<input type="text" name="new_doctype" id="new_doctype" class="form-control" placeholder="New Document Type" />
										</div>
									</div>


									<div class="form-group">
										<label for="domain" class="col-sm-3 control-label">Domain</label>
										<div class="col-sm-3">
											<select id="domain" name="domain" class="selectpicker pull-left show-tick" data-show-subtext="true" title="Select a Domain" data-container="body">
												<option data-hidden="true"></option>
												<option value='opendomain' data-subtext="(open domain)">No Specific Domain</option>
												<option data-divider="true"></option>
											@foreach($uniqueDomains as $domainKey => $domain)
												@if($domainKey <> 'opendomain')
													<option value="{{ $domainKey }}">{{ $domain }}</option>
												@endif
											@endforeach
											</select>
										</div>

										<div class="col-sm-4">
											<input type="text" name="new_domain" id="new_domain" class="form-control" placeholder="New Domain" />
										</div>
									</div>
									
									
								</div>
							</div>
							
							<div class="panel-footer">
								{{ Form::button('Add Media', array('type' => 'submit', 'value' => 'upload', 'class' => 'btn btn-primary pull-right')) }} 
								<div class='clearfix'></div>
							</div>

								{{ Form::close() }}				
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

		// highlight of online source button
		$('#online_source').change(function() {
			$('#file_upload').removeClass('btn-success').addClass('btn-default');
			$('#online_source').selectpicker('setStyle', 'btn-success');

			// if sound and vision is selected, toggle the extra menu option
			if($(this).val() == 'source_beeldengeluid') {
				$('#soundandvision').show('slow');
			} else {
				$('#soundandvision').hide('slow');
			}
		});
		
		// highlight of file upload button
		$('#file_upload').click(function() {
			$('#file_upload').addClass('btn-success').removeClass('btn-default');
			$('#online_source').selectpicker('setStyle', 'btn-success', 'remove');
			$('#online_source').selectpicker('setStyle', 'btn-default', 'add');
			$('#online_source').val(0);
			$('#online_source').selectpicker('refresh');
			$('#soundandvision').hide('slow');
		});

		// when a document is selected, update the format, doctype and domain selection
		$('#document').change(function() {
			$('#format').val($('#document option:selected').attr('format'));
			$('#doctype').val($('#document option:selected').attr('doctype'));
			$('#domain').val($('#document option:selected').attr('domain'));
		
			// refresh selectpicker to update bootstrap. do not refresh the online_source selectpicker to prevent it from breaking
			$('#customize_configuration .selectpicker').selectpicker('refresh');
			$('#format').selectpicker('setStyle', 'btn-success');
			$('#doctype').selectpicker('setStyle', 'btn-success');
			$('#domain').selectpicker('setStyle', 'btn-success');
		});

		$('#format').change(function() {
			$('#format').selectpicker('setStyle', 'btn-success');
		});

		$('#doctype').change(function() {
			$('#doctype').selectpicker('setStyle', 'btn-success');
		});
		
		$('#domain').change(function() {
			$('#domain').selectpicker('setStyle', 'btn-success');
		});
		
		$('#new_doctype').keyup(function() {
			if($(this).val().length > 0) {
				$('#doctype').attr('disabled', true);
				$('#doctype').val(0);
				$('#doctype').selectpicker('refresh');	
				$('#doctype').selectpicker('setStyle', 'btn-success','remove');
			} else {
				$('#doctype').attr('disabled', false);
				$('#doctype').selectpicker('refresh');	
			}
		});
		
		$('#new_domain').keyup(function() {
			if($(this).val().length > 0) {
				$('#domain').attr('disabled', true);
				$('#domain').val(0);
				$('#domain').selectpicker('refresh');
				$('#domain').selectpicker('setStyle', 'btn-success','remove');
			} else {
				$('#domain').attr('disabled', false);
				$('#domain').selectpicker('refresh');			
			}
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

		});
	</script>
@stop
