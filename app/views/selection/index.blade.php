@extends('layouts.default')

@section('head')
		{{ stylesheet_link_tag('custom/selection.css') }}
@stop

@section('content')
				<!-- START Selection_content --> 
				<div class="col-xs-12">
					<div class='maincolumn CW_box_style'>
							@if (($items = Cart::content()) && count(Cart::content()) > 0)
							<h4> Selection </h4>
							<div class='table-responsive'>
								<table class='table table-striped tableSelection'>
									<thead>
										<th>Title</th>
										<th>Collection</th>
										<th>Domain</th>
										<th>Document type</th>
										<th>Created On</th>
										<th>Created By</th>
									</thead>
								<tbody>

								@foreach ($items as $item)
								<?php 
									$repository = new \mongo\Repository;
									$entity = $repository->find($item['id']); 
								?>
									<tr>
										<td>
											<div class='btn-group'>
												<a class='btn btn-default btn-sm col-xs-9' href='{{ URL::to('files/view?' . $entity->_id) }}'>
													<i class='fa fa-file-text fa-fw'></i>{{ $entity->title }}
												</a>
												<a class='btn btn-default btn-sm col-xs-3 dropdown-toggle' data-toggle='dropdown' href='#'>
													<span class='fa fa-caret-down fa-fw'></span>
												</a>
												<ul class='dropdown-menu pull-right'>
													<li><a href='{{ URL::to('files/view?' . $entity->_id) }}'><i class='fa fa-file-text-o fa-fw'></i>View</a></li>
													<li><a class='update_selection' href='{{ URL::to('selection/remove?selectionID=' . $item['rowid']) }}'><i class='fa fa-trash-o fa-fw'></i>Remove from selection</a></li>
												</ul>
												</div>
										</td>
										<td>{{ link_to('files/browse/' . $entity->type, $entity->type) }}</td>										
										<td>{{ link_to('files/browse/' . $entity->type . '/' . $entity->domain, $entity->domain) }}</td>										
										<td>{{ link_to('files/browse/' . $entity->type . '/' . $entity->domain . '/' . $entity->documentType, $entity->documentType) }}</td>										
										<td>{{ $entity->created_at }}</td>
										<td>{{ link_to('#', $entity->wasAttributedTo->firstname . ' ' . $entity->wasAttributedTo->lastname) }}</td>										
									</tr>
								@endforeach
									</tbody>
								</table>
							</div>
							@else
							<div class="panel panel-warning">
								<div class="panel-heading">
									<h4><i class="fa fa-shopping-cart fa-fw"></i>Notice</h4>
								</div>
								<div class="panel-body">
									No documents have been selected yet
								</div>
							</div>
							@endif
						</div>
					</div>
				</div>
				<!-- STOP Selection_content --> 				
@stop

@section('end_javascript')
	{{ javascript_include_tag('jquery.tablesorter.min.js') }}
	{{ javascript_include_tag('jquery.tablesorter.widgets.min.js') }}

	<script type="text/javascript">
		$(document).ready(function () {
			$(".table").tablesorter({
				theme : 'bootstrap',
				stringTo: "max",

				// initialize zebra and filter widgets
				widgets: ["filter"],

				widgetOptions: {
				// include child row content while filtering, if true
				filter_childRows  : false,
				// class name applied to filter row and each input
				filter_cssFilter  : 'tablesorter-filter',
				// search from beginning
				filter_startsWith : false,
				// Set this option to false to make the searches case sensitive 
				filter_ignoreCase : true
				}
			});	
		});
	</script>
@stop