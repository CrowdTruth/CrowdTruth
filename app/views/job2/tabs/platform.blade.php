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
						<h4>Choose platform</h4>
					</div>
					<div class="panel-body">
						{{ Form::model($jobconf, array('class' => 'form-horizontal jobconf', 'action' => array('JobsController2@postFormPart', 'details'), 'method' => 'POST'))}}
						<div data-toggle="buttons">
							<label>Select the platform you want to send your job to:</label>
							@foreach ($possible as $p)
								<label class="btn btn-primary <?php if(isset($jobconf['platform']) and in_array($p['short'], $jobconf['platform'])) echo ' active';?>">
						   		{{ Form::checkbox('platform[]', $p['short'], null, array('class'=>'platform-button') )}} {{ $p['long'] }}
						  		</label>
							@endforeach

						</div>
						{{ Form::hidden('platformpage', 'a') }}
						{{ Form::submit('Next', array('class' => 'btn btn-lg btn-primary pull-right')); }}
						{{ Form::close()}}					
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>		

@endsection

@stop