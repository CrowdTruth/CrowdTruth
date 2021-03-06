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
						<h4>Preview task and submit</h4>
					</div>
					<div class="panel-body">
						<fieldset>
							<legend>Properties</legend>
							{{ $table }}
						</fieldset>
						<br>
						<fieldset>	
							<legend>Questions</legend>
							This is based on the AMT HTML template.<br><br>

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
									
							<iframe width="890"; height="{{ $frameheight }}" seamless sandbox="allow-scripts" srcdoc="{{ htmlentities($question) }}"></iframe>
									<div class="carousel-caption" style="color:black; font-size:2em;">
								        {{ $count }}
								      </div>
									</div>
									<?php $active = ''; ?>
								@endforeach

							  </div>

							  <!-- Controls -->
							  <a class="left carousel-control" href="#question-carousel" data-slide="prev" style="background:none; height:50px; top:45%">
							    <span class="fa fa-chevron-left" style="color:black; position:absolute; top:50%"></span>
							  </a>
							  <a class="right carousel-control" href="#question-carousel" data-slide="next" style="background:none; height:50px; top:45%">
							    <span class="fa fa-chevron-right" style="color:black; position:absolute; top:50%"></span>
							  </a>
							</div>



						</fieldset><br><br>
						<button class="btn btn-default btn-lg pull-left" data-toggle="modal" data-target="#myModal">
						  Save settings
						</button>
		<!--				{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('JobsController@postSubmitFinal', 'order'), 'method' => 'POST')) }}
							{{ Form::submit('Submit and order', array('class' => 'btn btn-lg btn-primary pull-right')); }}
						{{ Form::close()}}	

						{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('JobsController@postSubmitFinal', 'sandbox'), 'method' => 'POST')) }}
							{{ Form::submit('Submit to sandbox', array('class' => 'btn btn-lg btn-default pull-right', 'style' => 'margin-right:20px')); }} -->
							{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('JobsController@postSubmitFinal', 'sandbox'), 'method' => 'POST')) }}
							{{ Form::submit('Create Job', array('class' => 'btn btn-lg btn-default pull-right', 'style' => 'margin-right:20px')); }}
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
						        {{ Form::open(array('action' => 'JobsController@postSaveDetails'))}}
						        	{{ Form::label('template', 'Pick a name. Use underscores instead of spaces. NB: there needs to be an HTML template for every jobdetails file.') }}
						        	<br>
						        	{{ Form::text('template', $template, array('id' => 'template', 'class' => 'form-control col-xs-6')) }}
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