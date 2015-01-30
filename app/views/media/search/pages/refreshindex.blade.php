@extends('layouts.default_new')

@section('content')
@section('pageHeader', 'Search index')

				<!-- START upload_content --> 
				<div class="col-xs-10 col-sm-offset-1">
					<div class='maincolumn CW_box_style'>
	@include('layouts.flashdata')	
	@include('media.layouts.nav_new')	

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4>Refresh Index</h4>
							</div>
							<div class="panel-body">
								{{ Form::open([ 'action' => 'MediaController@postRefreshindex', 'name' => 'theForm', 'id' => 'theForm' ]) }}
								{{ Form::hidden('next', '0', [ 'id' => 'next' ]) }}
								<div class="form-horizontal">
									<div class="col-xs-12">
										<p>The platform keeps a search index list. On this page you can rebuild this list by indexing all properties in the database. Doing so will delete the existing search index.</p>
										Two big assumptions are made to shrink list size:
										<ul>
										<li>withoutSpam, withSpam, withFilter, withoutFilter are ignored</li>
										<li># sign in field name is ignored</li>
										</ul>
									</div>
									<div class="col-xs-12">
										<div class="progress">
											<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
												0% Complete
											</div>
										</div>
									</div>
									<div class="col-xs-12 text-center">
										{{ Form::button('Refresh Index', [ 'id' => 'refreshButton', 'class' => 'btn btn-primary' ]) }}
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
			$(this).attr('disabled',true).html('<i class="fa fa-spinner fa-spin"></i> Refreshing');
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
					pct = Math.round((data.next / data.last) * 100);
					$('.progress-bar').css('width', pct+'%').attr('aria-valuenow', pct).text(pct + '%');
					if(data.next < data.last) {
						doRebuild(data.next);
					} else {
						window.location="{{ URL::to('media/listindex') }}";
					}
				}
			});
		}
	</script>
@stop
