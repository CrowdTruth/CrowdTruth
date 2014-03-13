@extends('preprocess.twrex.layouts.default')

@section('colWidth', 'col-xs-12')
@section('twrexContent')
				<!-- START preprocess/twrex/actions --> 

							<div class='table-responsive'>
								<table class='table table-striped'>
								<thead>
									<tr>
										<th>Selection</th>
										<th>Created On</th>
										<th>Created By</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>

								@foreach ($entities as $entity)
									<tr>
										<td style="width:40%">
											<div class='btn-group'>
												<a class='btn btn-default btn-sm col-xs-9' href='{{ URL::to('files/view?' . $entity['_id']) }}'>
													<i class='fa fa-file-text fa-fw'></i>
													<span>{{ $entity['title'] }}</span>
												</a>
												<a class='btn btn-default btn-sm col-xs-3 dropdown-toggle' data-toggle='dropdown' href='#'>
													<span class='fa fa-caret-down fa-fw'></span>
												</a>
												<ul class='dropdown-menu pull-right'>
													<li><a href='{{ URL::to('files/view?URI=' . $entity['_id']) }}'><i class='fa fa-file-text-o fa-fw'></i>View</a></li>
													<!-- <li><a class='update_selection' href='{{ URL::to('selection/remove?selectionID=' . $entity['rowid']) }}'><i class='fa fa-trash-o fa-fw'></i>Remove from selection</a></li> -->
												</ul>
												</div>
										</td>
										<td>{{ $entity->created_at }}</td>
										<td>{{ link_to('#', $entity->wasAttributedToUserAgent->firstname . ' ' . $entity->wasAttributedToUserAgent->lastname) }}</td>										
										<td>
<a class='btn btn-success' href='{{ URL::to('preprocess/twrex/preview?URI=' . $entity['_id']) }}'><i class="fa fa-search fa-fw"></i>Preview</a>
<a class='btn btn-success' href='{{ URL::to('preprocess/twrex/process?URI=' . $entity['_id']) }}'><i class="fa fa-gear fa-fw"></i>Process</a>
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
				<!-- STOP preprocess/twrex/actions --> 				
@stop