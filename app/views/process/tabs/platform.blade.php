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
						<h4>Choose platform</h4>
					</div>
					<div class="panel-body">
						{{ Form::model($jobconf, array('class' => 'form-horizontal jobconf', 'action' => array('ProcessController@postFormPart', 'details'), 'method' => 'POST'))}}
						<div data-toggle="buttons">
							<label>Select the platform you want to send your job to:</label>	
						  	<label class="btn btn-primary <?php if(is_array($jobconf->platform) and in_array('cf', $jobconf->platform)) echo ' active';?>">
						   		{{ Form::checkbox('platform[]', 'cf', null, array('id' => 'cf-button') )}} Crowdflower
						  	</label>
						  	<label class="btn btn-primary <?php if(is_array($jobconf->platform) and in_array('amt', $jobconf->platform)) echo ' active';?>">
						    	{{ Form::checkbox('platform[]', 'amt', null, array('id' => 'amt-button') )}} Mechanical Turk
						  	</label>
						</div>
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