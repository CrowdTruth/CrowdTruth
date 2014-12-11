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
								<h4>Current search index</h4>
							</div>
							<div class="panel-body">
								@if($labels)
								<table class='table table-striped table-condensed'>
									<tbody>
										<tr>
											<th>Key</th>
											<th>Name</th>
										</tr>
									@foreach($labels as $key=>$label)
										<tr>
											<td>{{ $key }}</td>
											<td>{{ $label }}</td>
										</tr>

									@endforeach
									</tbody>
								</table>
								@else
									The index is empty.
								@endif
							</div>
						</div>
					</div>
				</div>
				<!-- STOP upload_content --> 				
@stop
