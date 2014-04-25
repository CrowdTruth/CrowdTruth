@extends('media.preprocess.fullvideo.layouts.default')

@section('colWidth', 'col-xs-12')
@section('fullvideoContent')
				<!-- START preprocess/fullvideo/actions --> 

							<div class='table-responsive'>
								<table class='table table-striped'>
								<thead>
									<tr>
										<th>Title</th>
										<th>Created On</th>
										<th>Created By</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>

								@foreach ($entities as $entity)
									<tr>
										<td style="width:40%">
											<div class='btn-group btn-group-justified'>
												<a class='btn btn-default btn-sm col-xs-12' style='text-align:left !important' href='#'>
													<i class='fa fa-file-text fa-fw'></i>
													<span>{{ $entity['title'] }}</span>
												</a>
												</div>
										</td>
										<td>{{ $entity->created_at }}</td>
										<td>{{ link_to('#', $entity->wasAttributedToUserAgent->firstname . ' ' . $entity->wasAttributedToUserAgent->lastname) }}</td>										
										<td>
<a class='btn btn-success' href='{{ URL::to('media/preprocess/fullvideo/preview?URI=' . $entity['_id']) }}'><i class="fa fa-search fa-fw"></i>Preview</a>
<a class='btn btn-success' href='{{ URL::to('media/preprocess/fullvideo/process?URI=' . $entity['_id']) }}'><i class="fa fa-gear fa-fw"></i>Process</a>
										</td>
									</tr>
								@endforeach
									<tr style='display:none'>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>
<a class='btn' href='#' style='visibility:hidden;'><i class="fa fa-plus-circle fa-fw"></i>Preview</a>
<a class='btn btn-success' href='{{ URL::to('preprocess/twrex/process?URI=' . $entity['_id']) }}'><i class="fa fa-gears fa-fw"></i>Process All</a>
										</td>
									</tr>
									</tbody>
								</table>
							</div>
				<!-- STOP preprocess/fullvideo/actions --> 				
@stop
