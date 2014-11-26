@extends('media.preprocess.relex.layouts.default')

@section('preprocessHeader')
	Relex <small> actions </small>
@stop

@section('colWidth', 'col-xs-12')
@section('relexContent')
				<!-- START preprocess/relex/actions -->

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
												<a class='btn btn-default btn-sm col-xs-12' style='text-align:left !important' href='{{ URL::to('media/view?URI=' . $entity['_id']) }}'>
													<i class='fa fa-file-text fa-fw'></i>
													<span>{{ $entity['title'] }}</span>
												</a>
												</div>
										</td>
										<td>{{ $entity->created_at }}</td>
										<td>{{ $entity->wasAttributedToUserAgent?($entity->wasAttributedToUserAgent->firstname . ' ' .$entity->wasAttributedToUserAgent->lastname):$entity->user_id }}</td>
										<td>
<a class='btn btn-success' href='{{ URL::to('media/preprocess/relex/preview?URI=' . $entity['_id']) }}'><i class="fa fa-search fa-fw"></i> View File</a>
<a class='btn btn-primary' href='{{ URL::to('media/preprocess/text/configure?URI=' . $entity['_id']) }}'><i class="fa fa-gear fa-fw"></i> Preprocess</a>	
										</td>
									</tr>
								@endforeach
									</tbody>
								</table>
							</div>
				<!-- STOP preprocess/relex/actions -->
@stop
