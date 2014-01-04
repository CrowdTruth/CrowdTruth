@extends('layouts.default')

@section('head')
		{{ stylesheet_link_tag('custom/file.css') }}
@stop

@section('content')
				<div class="col-xs-12">
					<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')						
						<div class='row' style="margin-bottom:0px">
							<div class="col-xs-8">
								@include('files.nav')
							</div>
							<div class="col-xs-4 text-center">
								<h2 class='thumbHeader'>View <small>Entity</small></h2>
							</div>
						</div>
						<div class='row'>
							<div class="col-xs-8">
							@include('files.browse.breadcrumb', array('fileType' => $entity->fileType, 'domainType' => $entity->domain, 'documentType' => $entity->documentType, 'document' => $entity))
							</div>
							<div class="col-xs-4 text-center">
								<div class="btn-group tabButtons">
								  <a href='#entity' class="btn btn-default active"><i class='fa fa-file-text-o fa-fw'></i>Entity</a>
								  <a href='#activity' class="btn btn-default"><i class='fa fa-expand fa-fw'></i>Activity</a>
								  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								    Actions <span class="caret"></span>
								  </button>
								  <ul class="dropdown-menu pull-right" role="menu">
								    <li><a href='{{ URL::to('selection/add?URI=' . $entity['_id']) }}' class='update_selection'><i class="fa fa-plus-circle fa-fw"></i>Add to selection</a></li>
								    <li><a href='{{ URL::to('files/delete?URI=' . $entity['_id']) }}'><i class='fa fa-trash-o fa-fw'></i>Delete document</a></li>
								  </ul>								
								</div>								
							</div>
						</div>

						<div class="tab-content">
							<div class="tab-pane fade in active" id="entity">
								<?php $entitySeparatedByNewline = explode("\n", $entity['content']); ?>
								<div class='table-responsive'>
									<table class='table table-striped'>
										<tbody>
											<tr>
												<td>URI</td>
												<td>
													<a href='#'>{{ $entity->_id }}</a>
												</td>
											</tr>
											<tr>
												<td>Title</td>
												<td>{{ $entity->title }}</td>
											</tr>
											<tr>
												<td>Extension</td>
												<td>{{ $entity->extension }}</td>
											</tr>
											<tr>
												<td>Collection</td>
												<td>{{ link_to('files/browse/text/', $entity->fileType) }}</td>
											</tr>
											<tr>
												<td>Domain</td>
												<td>{{ link_to('files/browse/text/'. $entity->domain, $entity->domain) }}</td>
											</tr>
											<tr>
												<td>Document type</td>
												<td>{{ link_to('files/browse/text/'. $entity->domain . '/' . $entity->documentType, $entity->documentType) }}</td>
											</tr>
											<tr>
												<td>Created by</td>
												<td>{{ link_to('#', $entity->wasAttributedTo->firstname . ' '. $entity->wasAttributedTo->lastname) }}</td>
											</tr>
										</tbody>
									</table>
									<table class='table table-striped tableContent'>
										<thead>
											<th>Line Number</th>
											<th>Content</th>
										</thead>
										<tbody class='content'>
											@foreach ($entitySeparatedByNewline as $linenumber => $lineval)
											<tr>
												<td><strong> {{ $linenumber }} </strong></td>
												<td>
													 {{ $lineval }}
												</td>
											</tr>
											@endforeach
										</tbody>
									</table>							
								</div>
							</div>
							<div class="tab-pane fade" id="activity">
								<div class='table-responsive'>
									<table class='table table-striped'>
										<tbody>
											<tr>
												<td>URI</td>
												<td>
													<a href='#'>{{ $entity->wasGeneratedBy->_id }}</a>
												</td>
											</tr>
											<tr>
												<td>Type</td>
												<td>{{ $entity->wasGeneratedBy->type }}</td>
											</tr>
											<tr>
												<td>Label</td>
												<td>{{ $entity->wasGeneratedBy->label }}</td>
											</tr>
											<tr>
												<td>Time</td>
												<td>{{ $entity->wasGeneratedBy->created_at }}</td>
											</tr>
											<tr>
												<td>User agent</td>
												<td>{{ link_to('#', $entity->wasAttributedTo->firstname . ' '. $entity->wasAttributedTo->lastname) }}</td>
											</tr>
											<tr>
												<td>Software agent</td>
												<td>{{ link_to($entity->wasGeneratedBy->software_id,  $entity->wasGeneratedBy->software_id) }}</td>
											</tr>
										</tbody>

							</div>
						</div>
					</div>
				</div>
@stop

@section('end_javascript')
<script type="text/javascript">
		$(document).ready(function () {
			$('.tabButtons > a').click(function (e) {
			  e.preventDefault();

			  $('.tabButtons > a').removeClass('active');
			  $(this).addClass('active');

			  $('.thumbHeader small').text($(this).text());
			  $(this).tab('show')
			})
		});
	</script>
@stop