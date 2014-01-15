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
						<h4>Submit your job</h4>
					</div>
					<div class="panel-body">
						<div>
							Here goes overview of questions and an overview of the (projected) costs of the job.
						</div>
						{{ Form::submit('Submit', array('class' => 'btn btn-lg btn-success')); }}
						{{ Form::close()}}					
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>		
@endsection
@section('end_javascript')
<script>
</script>
@endsection
@stop