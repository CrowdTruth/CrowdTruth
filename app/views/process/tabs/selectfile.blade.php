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
{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('ProcessController@postFormPart', 'submit'), 'method' => 'POST'))}}
{{ $temp or '' }}
						{{ Form::submit('Next', array('class' => 'btn btn-lg btn-primary pull-right')); }}
						{{ Form::close()}}		
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>		


@stop