@extends('layouts.default')

@section('content')
				<!-- START search_content --> 
				<div class="col-xs-12">
					<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')						
@include('files.layouts.nav')
						<div class='tab'>
							<div class='row'>
								<div class='col-xs-3'>
									{{ Form::open(array('url' => 'api', 'class' => 'form', 'role' => 'form', 'method' => 'get')) }}

										@if(count($searchFields['formats']) > 0)
										<div class="form-group">
											<label for="format">Format</label>
											<select name="format" id="format" class="form-control">
												@foreach($searchFields['formats'] as $format)
												<option value="{{ $format }}">{{ $format }}</option>
												@endforeach
											</select>
										</div>
										@endif

										@if(count($searchFields['domains']) > 0)
										<div class="form-group">
											<label for="format">Domains</label>
											@foreach($searchFields['domains'] as $domain)								
											<div class="checkbox">
												<label>
													<input name="domain[]" type="checkbox" value="{{ $domain }}"> {{ $domain }}
												</label>
											</div>
											@endforeach
										</div>
										@endif

										@if(count($searchFields['documentTypes']) > 0)
										<div class="form-group">
											<label for="documentTypes">Document-Types</label>
											@foreach($searchFields['documentTypes'] as $documentType)								
											<div class="checkbox">
												<label>
													<input name="documentType[]" type="checkbox" value="{{ $documentType }}"> {{ $documentType }}
												</label>
											</div>
											@endforeach
										</div>
										@endif

										@if(isset($searchFields['userAgents']))
										<div class="form-group">
											<label for="userAgents">User Agents</label>
											@foreach($searchFields['userAgents'] as $userAgent)						
											<div class="checkbox">
												<label>
													<input name="userAgent[]" type="checkbox" value="{{ $userAgent->_id }}"> {{ $userAgent->firstname . ' ' . $userAgent->lastname }}
												</label>
											</div>
											@endforeach
										</div>
										@endif

									<button type="submit" class="btn btn-default">Submit</button>
									</form>
								</div>

								<div class='col-xs-9'>
									To be added
								</div>
							</div>
						</div>
				</div>
				<!-- STOP search_content --> 				
@stop

@section('end_javascript')

	<script type="text/javascript">
		$('select').on('change', function() {
		  window.location = '{{ URL::to('files/search/') }}/' + $(this).val();
		});
	</script>

@stop