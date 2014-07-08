@extends('layouts.default')

@section('content')

<div class="col-xs-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			@include('job.nav')
			@include('layouts.flashdata')
			<div>
				<div class="panel panel-default">		
					<div class="panel-heading">
						<h4>Choose platform-specific options</h4>
					</div>
					<div class="panel-body">
						{{ Form::model($jobconf, array('class' => 'form-horizontal jobconf', 'action' => array('JobsController@postFormPart', 'nextplatform'), 'method' => 'POST'))}}
						<input type="hidden" name="platformid" value="cf" />
						<div id="cf-div" style="padding: 10px;">
							<fieldset>
								<legend>CrowdFlower</legend> 
								{{ Form::label('workerunitsPerWorker', 'Max workerunits per worker', array('class'=>'col-xs-4 control-label', 'data-toggle'=> 'tooltip', 'title'=>'Amount of units a worker is allowed to annotate')) }}
								<div class="input-group col-xs-2">
									{{ Form::input('number', 'workerunitsPerWorker', null, array('class'=>'form-control input-sm', 'min' => '1')) }}
								</div>
								<br>
								{{ Form::label('countries[]', 'Countries', array('class'=>'col-xs-4 control-label')) }}
								<div class="input-group col-xs-2">
									{{ Form::select('countries[]', Config::get('crowdflower::allcountries'), null, array('id' => 'countries', 'class'=>'selectpicker', 'data-live-search' => 'true', 'multiple', 'title' => 'Select countries...', 'data-selected-text-format' => 'count>3')) }}
								<br>
								<a id='deselectcountries' class='btn btn-small'>None</a>
								<a id='englishcountries' class='btn btn-small' title='{{ Config::get("crowdflower::englishcountries")}}'>English</a>
								<a id='customcountries' class='btn btn-small' title='{{ Config::get("crowdflower::customcountries")["countries"] }}'>{{ Config::get('crowdflower::customcountries')['language'] }}</a>
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

@section('platformend')
<script>
$('.selectpicker').selectpicker('render');

$('#deselectcountries').click(function(){
	$('#countries').selectpicker('deselectAll');
})

$('#englishcountries').click(function(){
	$('#countries').selectpicker('val', {{ Config::get('crowdflower::englishcountries') }}).selectpicker('render'); //'IE', 'NZ', 'JA'
})

$('#customcountries').click(function(){
	$('#countries').selectpicker('val', {{ Config::get('crowdflower::customcountries')['countries'] }}).selectpicker('render'); //'AW', 
})
</script>
@stop