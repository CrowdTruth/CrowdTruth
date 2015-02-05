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
								<h4>Current search index <a class='btn btn-primary pull-right' href="{{ URL::to('media/refreshindex') }}">Rebuild Index</a></h4>
							</div>
							<div class="panel-body">
								@if($keys)
								<table class='table table-striped table-condensed'>
									<thead>
										<tr>
											<th></th>
											<th>Index</th>
											<th>Document Types</th>
										</tr>
									</thead>
									<tbody>
									@foreach($keys as $key=>$label)
										<tr>
											<td><i class="fa {{ $formats[$keys[$key]['format']] }}"></i></td>
											<td class='text-left'>{{ $keys[$key]['label'] }}</td>
											<td>
											@foreach ($keys[$key]['documents'] as $document)
												<span class="label label-default">{{ $document }}</span>
											@endforeach
											</td>
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
