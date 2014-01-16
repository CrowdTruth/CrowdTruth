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
							<table class="table table-striped">
							<!--<tr><th>Property</th><th>Value</th></tr>-->
							@foreach ($crowdtask->toArray() as $key=>$val)
								<tr><th>{{ $key }}</th><td>{{ $val }}</td></tr>
								
							@endforeach
							</table>
						</fieldset>
						<br>
						<fieldset>	
							<legend>Questions</legend>
							<div id="question-carousel" class="carousel slide" data-ride="carousel">
							  <!-- Indicators -->
							  <ol class="carousel-indicators">
							    <li data-target="#question-carousel" data-slide-to="0" class="active"></li>
							    <li data-target="#question-carousel" data-slide-to="1"></li>
							    <li data-target="#question-carousel" data-slide-to="2"></li>
							  </ol>

							  <!-- Wrapper for slides -->
							  <div class="carousel-inner" >
							  	<?php $count = 0; ?>
							 	@foreach($questions as $question)
							 		<?php $count++; ?>
									 <div class="item <?php if($count == 1) echo 'active'; ?>">
									{{ preg_replace('#<script(.*?)>(.*?)</script>#is', '', $question); /* TODO: this is probably not safe ;) */}}
									<div class="carousel-caption" style="color:black; font-size:2em;">
								        {{ $count }}
								      </div>
									</div>
									<?php $active = ''; ?>
								@endforeach

							  </div>

							  <!-- Controls -->
							  <a class="left carousel-control" href="#question-carousel" data-slide="prev" style="background:none">
							    <span class="fa fa-chevron-left" style="color:black; position:absolute; top:50%"></span>
							  </a>
							  <a class="right carousel-control" href="#question-carousel" data-slide="next" style="background:none">
							    <span class="fa fa-chevron-right" style="color:black; position:absolute; top:50%"></span>
							  </a>
							</div>


						</fieldset>
						

						{{ Form::model($crowdtask, array('class' => 'form-horizontal crowdtask', 'action' => 'ProcessController@postSubmit', 'method' => 'POST')) }}
						@if(Session::has('flashError'))
							{{ Form::submit('Submit', array('class' => 'btn btn-lg btn-primary pull-right', 'disabled')); }}
						@else 
							{{ Form::submit('Submit', array('class' => 'btn btn-lg btn-primary pull-right')); }}
						@endif
						{{ Form::close()}}					
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>		
@endsection
@stop