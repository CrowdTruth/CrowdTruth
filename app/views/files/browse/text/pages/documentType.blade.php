@extends('layouts.default')

@section('head')
		{{ stylesheet_link_tag('custom/file.css') }}
		{{ javascript_include_tag('holder.js') }}
@stop

@section('content')
				<!-- STOP files/browse/document -->
				<div class="col-xs-12">
					<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')

					<div class='row'>
						<div class="col-xs-8">
							@include('files.layouts.nav')
						</div>
						<div class="col-xs-4 text-center">
							<h2 class='thumbHeader'> {{$documentType }} &nbsp;<small>documents</small></h2>
						</div>
					</div>
					@include('files.browse.layouts.breadcrumb')
						<div class='table-responsive'>
							<table class='table table-striped'>
								<thead>
									<th>Title</th>
									<th>Created on</th>
									<th>Created by</th>
								</thead>
								<tbody>

							@foreach ($entities as $entity)
									<tr>
										<td class='file_id'>
											<div class='btn-group'>
												<a class='btn btn-default btn-sm col-xs-9' href='{{ URL::to('files/view?URI=' . $entity['_id']) }}'>
													<i class='fa fa-file-text fa-fw'></i>{{ $entity['title'] }}
												</a>
												<a class='btn btn-default btn-sm col-xs-3 dropdown-toggle' data-toggle='dropdown' href='#'>
													<span class='fa fa-caret-down fa-fw'></span>
												</a>
												<ul class='dropdown-menu pull-right'>
													<li><a class='update_selection' href='{{ URL::to('selection/add?URI=' . $entity['_id']) }}'><i class='fa fa-plus-circle fa-fw'></i>Add to selection</a></li>
													<li class='disabled'><a href='#'><i class='fa fa-pencil fa-fw'></i>Edit</a></li>
													<li><a href='{{ URL::to('files/view?URI=' . $entity['_id']) }}'><i class='fa fa-file-text-o fa-fw'></i>View</a></li>
													<li><a class='delete_document' href='{{ URL::to('files/delete?URI=' . $entity['_id']) }}'><i class='fa fa-trash-o fa-fw'></i>Delete document</a></li>
												</ul>
											</div>
										</td>

										@if($entity->wasAttributedToUserAgent)
										<td>{{ $entity['created_at'] }}</td>
										<td>{{ link_to('#', $entity->wasAttributedToUserAgent->firstname . ' ' . $entity->wasAttributedToUserAgent->lastname) }}</td>										
										@endif

									</tr>
							@endforeach

								</tbody>
							</table>
						</div>

					</div>
				</div>
				<!-- STOP files/browse/document --> 				
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