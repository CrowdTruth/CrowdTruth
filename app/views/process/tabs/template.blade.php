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
						<h4>Select your template</h4>
					</div>
					<div class="panel-body">
						{{ Form::model($jobconf, array('class' => 'form-horizontal jobconf', 'action' => array('ProcessController@postFormPart', 'details'), 'method' => 'POST'))}}
			  
						<div id="jstree"></div>
						<fieldset>	
							<style type="text/css">
							 .jstree li > a > .jstree-icon {  display:none !important; } 
							</style>
							{{ Form::hidden('template', $currenttemplate, array('id' => 'template')) }}
							<br><br>
							<iframe id ="question" src="/templates/{{ $currenttemplate }}.html" seamless sandbox="allow-scripts" width="890" height="600"></iframe>
						</fieldset>
						<br>
						<br>
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