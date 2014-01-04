@extends('layouts.default')

@section('content')
				<!-- START preprocess/chang/preview --> 
				<div class="col-xs-12">
					<div class='maincolumn CW_box_style'>
@include('preprocess.nav')
						<div class='tab'>

							@if (isset($document) && count($document) > 0)

							<div class='table-responsive'>
								<table class='table table-striped'>
									<thead>
										<tr>
											<th>Line</th>
											<th>TWrex-relation</th>
											<th>Term 1</th>
											<th>B1</th>
											<th>E1</th>
											<th>Term 2</th>
											<th>B2</th>
											<th>E2</th>
											<th>Sentence</th>
										</tr>								
									</thead>
									<tbody>
										@foreach ($document as $lineNumber => $lineValue)
										<tr>
											<td>{{ $lineNumber }}</td>
											<td>{{ $lineValue['TWrex-relation'] }}</td>
											<td>{{ $lineValue['factors']['first']['text'] }}</td>
											<td>{{ $lineValue['factors']['first']['startIndex'] }}</td>
											<td>{{ $lineValue['factors']['first']['endIndex'] }}</td>
											<td>{{ $lineValue['factors']['second']['text'] }}</td>
											<td>{{ $lineValue['factors']['second']['startIndex'] }}</td>
											<td>{{ $lineValue['factors']['second']['endIndex'] }}</td>
											<td class='sentence'>{{ $lineValue['sentence']['text'] }}</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							@endif
		
						</div>
					</div>
				</div>
				<!-- STOP preprocess/chang/preview --> 				
@stop