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
				<!-- STOP upload_content ---> 				
@stop