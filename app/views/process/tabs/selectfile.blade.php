@extends('layouts.default')

@section('content')


<div class="col-xs-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			@include('process.nav')
			@include('layouts.flashdata')
			<div>
				<div class="panel panel-default">		
					<div class="panel-heading">
						<h4>Select files</h4>
					</div>
					<div class="panel-body">
						{{ $temp }}
						{{ Form::model($jobconf, array('class' => 'form-horizontal jobconf', 'action' => array('ProcessController@postFormPart', 'template'), 'method' => 'POST'))}}
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
												<!-- Below href is meant for view but left empty, see line 45 -->
												<a class='btn btn-default btn-sm col-xs-9' href=''>
													<i class='fa fa-file-text fa-fw'></i>
													<span>{{ $entity->title }}</span>
												</a>
												<a class='btn btn-default btn-sm col-xs-3 dropdown-toggle' data-toggle='dropdown' href='#'>
													<span class='fa fa-caret-down fa-fw'></span>
												</a>
												<ul class='dropdown-menu pull-right'>
													<!-- Find solution for document type view (aggregation of sentences, check getView method in FilesController and pages dir views/files/view/text) 
													This goes in href below and above: {{ URL::to('files/view?' . $entity['_id']) }} -->
													<li><a href=''><i class='fa fa-file-text-o fa-fw'></i>View</a></li>
													<li><a class='update_selection' href='{{ URL::to('selection/remove?selectionID=' . $entity['rowid']) }}'><i class='fa fa-trash-o fa-fw'></i>Remove from selection</a></li>
												</ul>
												</div>
										</td>
										<td>{{ $entity->created_at }}</td>
										<td>{{ link_to('#', $entity->wasAttributedTo->firstname . ' ' . $entity->wasAttributedTo->lastname) }}</td>										
										<td>
											<a class='btn btn-success' href='{{ URL::to('preprocess/chang/preview?' . $entity['_id']) }}'><i class="fa fa-search fa-fw"></i>Preview</a>
											<a class='btn btn-success' href='{{ URL::to('preprocess/chang/process?' . $entity['_id']) }}'><i class="fa fa-gear fa-fw"></i>Process</a>
										</td>
									</tr>


								@endforeach
									<tr style='display:none'>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>
											<a class='btn' href='#' style='visibility:hidden;'><i class="fa fa-plus-circle fa-fw"></i>Preview</a>
											<a class='btn btn-success' href=''><i class="fa fa-gears fa-fw"></i>Process All</a>
										</td>
									</tr>
									</tbody>
								</table>
							</div>
						

						{{ Form::submit('Next', array('class' => 'btn btn-lg btn-primary pull-right')); }}
						{{ Form::close()}}					
				
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>		


@stop