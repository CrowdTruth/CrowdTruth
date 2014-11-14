@extends('layouts.default_new')
@section('head')
	<script>
		function doRebuild(nextIndex) {
			console.log('	Rebuild from: ' + nextIndex);
			$('#next').val(nextIndex);
			formUrl = $("#theForm").attr("action");
			formData = $("#theForm").serialize();

			$.ajax({
				type: "POST",
				url: formUrl,
				data: formData,
				success: function(data) {
					pct = (data.next / data.last) * 100;
					pct = Math.round(pct * 100) / 100;
					msg = "Ready " + pct + "%";
					$("#status_area").html(msg);

					if(data.next < data.last) {
						doRebuild(data.next);
					}
				}
			});
		}
	</script>
@stop

@section('content')
@section('pageHeader', 'Search index')

				<!-- START upload_content --> 
				<div class="col-xs-10 col-sm-offset-1">
					<div class='maincolumn CW_box_style'>
	@include('layouts.flashdata')	
	@include('media.layouts.nav_new')	

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4>Rebuild index</h4>
								{{ link_to_action('MediaController@getListindex', 'View current index') }}
							</div>
							<div class="panel-body">
								{{ Form::open([ 'action' => 'MediaController@postRefreshindex', 'name' => 'theForm', 'id' => 'theForm' ]) }}
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
									<div class="form-group col-sm-offset-3 col-sm-5">
										NOTE: Two big assumptions are made to shrink list size:
										<ul>
										<li>withoutSpam, withSpam, withFilter, withoutFilter are ignored</li>
										<li># sign in field name is ignored</li>
										</ul>
									</div>
								</div>
								{{ Form::close() }}
							</div>
						</div>
					</div>
				</div>
				<!-- STOP upload_content --> 				
@stop
