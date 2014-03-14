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
						<h4>Choose platform-specific options</h4>
					</div>
					<div class="panel-body">
						{{ Form::model($jobconf, array('class' => 'form-horizontal jobconf', 'action' => array('ProcessController@postFormPart', 'submit'), 'method' => 'POST'))}}
						<div id="cf-div" style="padding: 10px;">
							<fieldset>
								<legend>CrowdFlower</legend> 
								{{ Form::label('annotationsPerWorker', 'Max annotations per worker', array('class'=>'col-xs-4 control-label')) }}
								<div class="input-group col-xs-2">
									{{ Form::input('number', 'annotationsPerWorker', null, array('class'=>'form-control input-sm', 'min' => '1')) }}
								</div>
								<br>
								{{ Form::label('countries[]', 'Countries', array('class'=>'col-xs-4 control-label')) }}
								<div class="input-group col-xs-2">
									{{ Form::select('countries[]', $countries, null, array('id' => 'countries', 'class'=>'selectpicker', 'data-live-search' => 'true', 'multiple', 'title' => 'Select countries...', 'data-selected-text-format' => 'count>3')) }}
								<br>
								<a id='deselectcountries' class='btn btn-small'>None</a>
								<a id='englishcountries' class='btn btn-small' title='{{ Config::get('crowdflower::englishcountries')}}'>English</a>
								<a id='customcountries' class='btn btn-small' title='{{ Config::get('crowdflower::customcountries')['countries'] }}'>{{ Config::get('config.customcountries')['language'] }}</a>
								</div>

							</fieldset><br>	
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