@extends('media.preprocess.layouts.default')

@section('preprocessHeader')
	Relex <small> actions </small>
@stop

@section('colWidth', 'col-xs-12')
@section('relexContent')
	<!-- START media/preprocess -->
	<div class='table-responsive'>
		<table class='table table-striped'>
		<thead>
			<tr>
				<th>File</th>
				<th class='text-center'>Project</th>
				<th class='text-center'>Created On</th>
				<th class='text-center'>Created By</th>
			</tr>
		</thead>
		<tbody>

		@foreach ($files as $entity)
			<tr>
				<td style="width:40%">
					<div class='btn-group btn-group-justified'>
						@if($entity['results'])
							<a class='btn btn-success btn-sm col-xs-12' style='text-align:left !important' href='{{ URL::to('media/preprocess/text/configure?URI=' . $entity['_id']) }}'>
						@else
							<a class='btn btn-default btn-sm col-xs-12' style='text-align:left !important' href='{{ URL::to('media/preprocess/text/configure?URI=' . $entity['_id']) }}'>
						@endif
							<i class='fa fa-file-text fa-fw'></i>
							<span>{{ $entity['title'] }}</span>
						</a>
					</div>
				</td>
				<td>{{ $entity->project }}</td>
				<td>{{ $entity->created_at }}</td>
				<td>{{ $entity->wasAttributedToUserAgent?($entity->wasAttributedToUserAgent->firstname . ' ' .$entity->wasAttributedToUserAgent->lastname):$entity->user_id }}</td>
			</tr>
		@endforeach

			</tbody>
		</table>
	</div>
	<!-- STOP media/preprocess -->
@stop
