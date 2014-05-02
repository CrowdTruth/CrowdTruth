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


						<div class="well">
							<p>On this page, you can select a template for the job you want the workers to perform. In the tree, the parent is the job's <i>type</i> and the child is the specific template in this category.
							</p>
							<p>
							 The preview you see below is how the job will look on Mechanical Turk. The variables between @{{curly_braces}} will be replaced with values from the batch or, in case of the @{{instructions}} variables, from the job settings. 
							</p>

						</div>
			  		<p>Displaying templates with format <b>{{$format}}</b>, based on the selected batch.</p>
						<div id="jstree"></div>
						<br>
						<button class="btn btn-default" data-toggle="modal" data-target="#myModal">
						  Upload...
						</button>
						<br>
						<fieldset>	
							<style type="text/css">
							 .jstree li > a > .jstree-icon {  display:none !important; } 
							</style>
							<br><br>
							<iframe id ="question" src="/templates/{{ $currenttemplate }}.html" seamless sandbox="allow-scripts" width="890" height="600"></iframe>
						</fieldset>
						<br>
						<br>
						{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('ProcessController@postFormPart', 'platform'), 'method' => 'POST'))}}
						{{ Form::hidden('template', $currenttemplate, array('id' => 'template')) }}
						{{ Form::submit('Next', array('class' => 'btn btn-lg btn-primary pull-right')); }}
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
						        <h4 class="modal-title">Upload template</h4>
						      </div>
						      <div class="modal-body">
								<div class="form-group">
									{{ Form::open(array('action' => 'ProcessController@postUploadTemplate', 'files' => 'true')) }}
									<p>You can upload your own templates here. Currently, HTML is supported for Amazon Mechanical Turk and CML for Crowdflower. Any CSS or JS will need to be inserted into the AMT HTML and uploaded as separate files with the same name for CF. Read the <a href="/info">documentation</a> for more info.</p> 
									<p>
									You can save the job settings in the 'Submit' tab.
									</p>
									<p>
									In the near future, we will have our own format for questions that will convert to both (and more) types of template, so you'll have to create only one.
									</p>
									{{ Form::text('type', null, array('class' => 'form-control col-xs-6', 'placeholder' => 'Type (like \'FactSpan\' or \'RelEx\')')) }}<br><br>
									<input type="file" name="files[]" class="btn uploadInput" multiple style="display:inline-block"/>

								</div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								{{ Form::submit('Upload', array('class' => 'btn btn-default', 'style' => 'display:inline-block')) }}
								{{ Form::close()}}	
						      </div>
						    </div><!-- /.modal-content -->
						  </div><!-- /.modal-dialog -->
						</div><!-- /.modal -->
						<!-- /HIDDEN -->
@endsection
@stop