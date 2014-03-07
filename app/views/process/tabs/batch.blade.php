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
						<h4>Select Batch</h4>
					</div>
					<div class="panel-body">

					
						{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('ProcessController@postFormPart', 'template'), 'method' => 'POST'))}}
							<div class='table-responsive'>
								<table class='table table-striped'>
									<thead>
										<tr>
											<th></th>
											<th>ID</th>
											<th>title</th>
											<th>Format</th>
											<th>DocumentType</th>
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
												{{ $batch->format }}
											</td>
											<td>
												{{ $batch->documentType }}
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