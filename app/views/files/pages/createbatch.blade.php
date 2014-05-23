@extends('layouts.default')

@section('content')
				<!-- START createbatch_content --> 
				<div class="col-xs-10 col-sm-offset-1">
					<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')						
@include('files.layouts.nav')

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4><i class="fa fa-upload fa-fw"></i>Create Batch</h4>
							</div>
							<div class="panel-body">							

								{{ Form::open(array('action' => 'FilesController@anyBatch')) }}
								<div class="form-horizontal">

									<div class="form-group">
										<label for="format" class="col-sm-3 control-label">Format</label>
										<div class="col-sm-5">
											<select name="format" class="form-control">
												<option value="{{ $fields[1] }}">{{ $fields[1] }}</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="domain" class="col-sm-3 control-label">Domain</label>
										<div class="col-sm-5">
											<select name="domain" class="form-control">
												<option value="{{ $fields[2] }}">{{ $fields[2] }}</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="documentType" class="col-sm-3 control-label">Document-Type</label>
										<div class="col-sm-5">
											<select name="documentType" class="form-control">
												<option value="{{ $fields[3] }}">{{ $fields[3] }}</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="units" class="col-sm-3 control-label">Units In Batch</label>
										<div class='table-responsive col-sm-9'>
											<table class='table table-striped datatable_content'>
												<thead>
													<tr data-field-query="field[documentType][]=relex">
														<th>Included</th>	
														<th>ID</th>	
													</tr>
												</thead>
												<tbody>
													@foreach($units as $unit)
													<tr>
														<td>
															<input type="checkbox" name="units[]" value="{{ $unit }}" checked="checked" />
														</td>
														<td>
															{{ $unit }}
														</td>
													</tr>
													@endforeach
											    </tbody>
									    	</table>
										</div>
									</div>

									<div class="form-group">
										<label for="batch_title" class="col-sm-3 control-label">Batch Title</label>
										<div class="col-sm-5">
											<input type="text" class="form-control" placeholder="Batch Title" name="batch_title" required >
										</div>							
									</div>

									<div class="form-group">
										<label for="batch_description" class="col-sm-3 control-label">Batch description</label>
										<div class="col-sm-5">
											<textarea class="form-control" rows="3" name="batch_description" required ></textarea>
										</div>							
									</div>

									<div class="form-group">
										<div class="col-sm-offset-3 col-sm-5">
										{{ Form::button('Submit', array('type' => 'submit', 'class' => 'btn btn-info')) }} 
										</div>
									</div>
								</div>

								{{ Form::close() }}				

							</div>
						</div>
					</div>
				</div>
				<!-- STOP createbatch_content --> 				
@stop