@extends('layouts.default')

@section('content')
				<!-- START upload_content --> 
				<div class="col-xs-4">
					<div class='sidebar CW_box_style'>
						sidebar
					</div>
				</div>
				<div class="col-xs-8">
					<div class='maincolumn CW_box_style'>
@include('files.nav')

						<div class='tab'>
						@if (isset($status_upload['error']))
							<div class="panel panel-danger">
								<div class="panel-heading">
									<h4><i class="fa fa-exclamation-triangle fa-fw"></i>Error</h4>
								</div>
								<div class="panel-body">
									<ul class="list-group">					
							@foreach ($status_upload['error'] as $status_message)
								<li class="list-group-item"><span class='message'> {{ $status_message }} </li>
							@endforeach
									</ul>
								</div>
							</div>
						@endif

						@if (isset($status_upload['success']))
							<div class="panel panel-success">
								<div class="panel-heading">
									<h4><i class="fa fa-check fa-fw"></i>Success</h4>
								</div>
								<div class="panel-body">
							@foreach ($status_upload['success'] as $status_message)
								<li class="list-group-item"><span class='message'> {{ $status_message }} </li>
							@endforeach
								</div>
							</div>
						@endif

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4><i class="fa fa-upload fa-fw"></i>File input</h4>
								</div>
								<div class="panel-body">

									{{ Form::open(array('action' => 'FilesController@postUpload', 'files' => 'true')) }}

									<div class="form-group">
										<input type="file" name="files[]" multiple />
										<p class="help-block">Allowed filetypes are: txt | csv | pdf </p>
									</div>


									{{ Form::button('Submit', array('type' => 'submit', 'value' => 'upload', 'class' => 'btn btn-default')) }} 
									{{ Form::close() }}									

									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- STOP upload_content --> 				
@stop