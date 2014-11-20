@extends('layouts.default_new')
@section('head')
	<script>
		function doRebuild(nextIndex) {
			console.log('DoRebuild called!');
			
			$('#next').val(nextIndex);
			formUrl = $("#theForm").attr("action");
			formData = $("#theForm").serialize();

			console.log('Data: ' + formData);
			$.ajax({
				type: "POST",
				url: formUrl,
				data: formData,
				success: function(data) {
					console.log('Callback Ok!');
					console.log('Callback data: ' + data);
					console.log('Callback data.next: ' + data.next);
					console.log('Callback data.last: ' + data.last);
					$("#status_area").html(data);
				}
			});
		}
	</script>
@stop

@section('content')
@section('pageHeader', 'Refresh Index')

				<!-- START upload_content --> 
				<div class="col-xs-10 col-sm-offset-1">
					<div class='maincolumn CW_box_style'>
	@include('layouts.flashdata')	
	@include('media.layouts.nav_new')	

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4>Form</h4>
							</div>
							<div class="panel-body">							
								{{ Form::open(array('action' => 'MediaController@postRefreshindex', 'name' => 'theForm')) }}
								{{ Form::hidden('next', '0', [ 'id' => 'next' ]) }}
								<div class="form-horizontal">
									<div class="form-group">
										<textarea name="status_area" id="status_area"></textarea> 
									</div>

									<div class="form-group">
										<div class="col-sm-offset-3 col-sm-5">
										{{ Form::button('Rebuild', [ 'onClick' => 'doRebuild(0);', 'class' => 'btn btn-info' ]) }}
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
