@extends('layouts.default')

@section('content')

<div class="col-xs-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			@include('job2.nav')
			@include('layouts.flashdata')
			<div>
				<div class="panel panel-default">		
					<div class="panel-heading">
						<h4>Select Batch</h4>
					</div>
					<div class="panel-body">
						{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('JobsController2@postFormPart', 'template'), 'method' => 'POST'))}}
							<div class='table-responsive'>
								<table class='table table-striped'>
									<thead>
										<tr>
											<th></th>
											<th>ID</th>
											<th>Title</th>
											<th>Description</th>
											<th>Size</th>
											<th>Format</th>
											<th>Domain</th>
										</tr>
									</thead>
									<tbody>
										@foreach($batches as $batch)
										<tr>
											<td>
												{{ Form::radio("batch", $batch->_id, ($selectedbatchid==$batch->_id ? true : false)) }}
											</td>
											<td>
												{{ $batch->_id }}
											</td>
											<td>
												{{ $batch->title }}
											</td>
											<td>
 												{{ $batch->content }}
											</td>	
											<td>
												{{ count($batch->parents) }}
											</td>
											<td>
												{{ $batch->format }}
											</td>
											<td>
												{{ $batch->domain }}
											</td>
										</tr>
										@endforeach
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