@extends('layouts.default_new')

@section('content')
@section('pageHeader', 'Update Database')

				<!-- START upload_content --> 
				<div class="col-xs-10 col-sm-offset-1">
					<div class='maincolumn CW_box_style'>
	@include('layouts.flashdata')	
	@include('media.layouts.nav_new')	

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4>Update Database</h4>
							</div>
							<div class="panel-body">
								{{ Form::open([ 'action' => 'MediaController@postUpdatedb', 'name' => 'theForm', 'id' => 'theForm' ]) }}
								{{ Form::hidden('next', '0', [ 'id' => 'next' ]) }}
								<div class="form-horizontal">
									<div class="col-xs-12">
										<p>This will update the existing units in the database to use the new datamodel. This will:
										<ul>
										<li>Remove format and domain from old entity URI's</li>
										<li>Add or update project owner of the entity</li>
										</ul>
										<p><strong>Warning: This process cannot be undone</strong></p>
									</div>
									<div class="col-xs-12">
										<div class="progress">
											<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
												0% Complete
											</div>
										</div>
									</div>
									<div class="col-xs-12 text-center">
										{{ Form::button('Update database', [ 'id' => 'refreshButton', 'class' => 'btn btn-primary' ]) }}
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
	<script>
		$('#refreshButton').click(function() {
			doRebuild(0);
			$(this).attr('disabled',true).html('<i class="fa fa-spinner fa-spin"></i> Updating');
			$('.progress-bar').css('width', '1%').attr('aria-valuenow', 1).text('1%');
		});
	
		function doRebuild(nextIndex) {
			$('#next').val(nextIndex);
			formUrl = $("#theForm").attr("action");
			formData = $("#theForm").serialize();

			$.ajax({
				type: "POST",
				url: formUrl,
				data: formData,
				success: function(data) {
					console.dir(data.log);
					pct = Math.round((data.next / data.last) * 100);
					$('.progress-bar').css('width', pct+'%').attr('aria-valuenow', pct).text(pct + '%');
					if(data.next < data.last) {
						doRebuild(data.next);
					} else {
						console.log('Complete!');
					}
				}
			});
		}
	</script>
@stop
