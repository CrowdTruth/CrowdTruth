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
						<h4>Preview task and submit</h4>
					</div>
					<div class="panel-body">
						<fieldset>
							<legend>Properties</legend>
							@foreach ($crowdtask->toArray() as $key=>$val)
								<label>{{ $key }}</label> {{ $val }}<br>
							@endforeach
						</fieldset>
						<br>
						<br>
						<fieldset>	
							<legend>Question</legend>
							The first parameters of the CSV-file have been loaded.<br><br>
							@foreach($questions as $question)
							<div style="border:1px solid darkblue; padding:20px;">
								{{ $question /* TODO: this is probably not safe ;) */}}
							</div>
							@endforeach
						</fieldset>
						

						{{ Form::model($crowdtask, array('class' => 'form-horizontal crowdtask', 'action' => 'ProcessController@postSubmit', 'method' => 'POST')) }}

						{{ Form::submit('Submit', array('class' => 'btn btn-lg btn-primary pull-right')); }}
						{{ Form::close()}}					
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>		
@endsection
@stop