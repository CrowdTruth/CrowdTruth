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
							@foreach ($jobconf->toArray() as $key=>$val)
								@if (is_array($val))
								<tr><th>{{ $key }}</th><td><pre><?php var_dump($val) ?></pre></td></tr>
								@else
								<tr><th>{{ $key }}</th><td>{{ $val }}</td></tr>
								@endif
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
									{{ $question }}
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
						<button class="btn btn-default btn-lg pull-left" data-toggle="modal" data-target="#myModal">
						  Save settings
						</button>

						{{ Form::model($jobconf, array('class' => 'form-horizontal jobconf', 'action' => 'ProcessController@postSubmitFinal', 'method' => 'POST')) }}
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

@section("modal")
						<!-- HIDDEN -->
						<div class="modal fade" id="myModal" tabindex="-1">
						  <div class="modal-dialog">
						    <div class="modal-content">
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						        <h4 class="modal-title">Save job details</h4>
						      </div>
						      <div class="modal-body">
						      	<style type="text/css">
								 .jstree li > a > .jstree-icon {  display:none !important; } 
								</style>
						        <div id="jstree"></div>
						        <br>
						        <div>
						        {{ Form::open(array('action' => 'ProcessController@postSaveDetails'))}}
						        	{{ Form::label('template', 'Pick a name. Use underscores instead of spaces. NB: there needs to be an HTML template for every jobdetails file.') }}
						        	<br>
						        	{{ Form::text('template', $jobconf->template, array('id' => 'template', 'class' => 'form-control col-xs-6')) }}
						        </div>
						      </div>
						      <br>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						        {{ Form::submit('Save', array('class' => 'btn btn-primary'))}}       
						        {{ Form::close()}}
						      </div>
						    </div><!-- /.modal-content -->
						  </div><!-- /.modal-dialog -->
						</div><!-- /.modal -->
						<!-- /HIDDEN -->
@endsection
@stop