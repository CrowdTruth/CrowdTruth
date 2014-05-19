@extends('layouts.default')
@section('content')
			<!-- START /index --> 			
			<div class="col-xs-8 col-md-offset-2">
				<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')	
	<div class="page-header text-center" style="margin:10px;">
						<h2><i class="fa fa-angle-left" style="float:left; color:#999; display:inline-block; cursor:pointer" onclick="javascript:window.history.back()"></i>Api Examples</h2>
					</div>
					<div class="row">
						<div class="col-xs-10 col-xs-offset-1"  style="padding-bottom:40px; padding-top:20px">

<ul class="list-group">
  <li class="list-group-item"><a href='http://crowdtruth.org/api/v1?field[documentType]=twrex-structured-sentence&limit=1&pretty'>IBM medical relation extraction sentence</a></li>
  <li class="list-group-item"><a href='http://crowdtruth.org/api/v1?field[documentType]=twrex-structured-sentence&limit=1&wasAttributedTo&pretty'>IBM medical relation extraction sentence - wasAttributedTo</li>
  <li class="list-group-item"><a href='http://crowdtruth.org/api/v1?field[documentType]=twrex-structured-sentence&limit=1&wasDerivedFrom&pretty'>IBM medical relation extraction sentence - wasDerivedFrom</li>  
  <li class="list-group-item"><a href='http://crowdtruth.org/api/v1?field[documentType]=twrex-structured-sentence&limit=1&wasGeneratedBy&wasAssociatedWith&pretty'>IBM medical relation extraction sentence - wasGeneratedBy and wasAssociatedWith</li>
  <li class="list-group-item"><a href='http://crowdtruth.org/api/v1?field[documentType]=batch&limit=1&pretty'>Batch of IBM medical relation extraction sentences</li>
</ul>							

						</div>						
					</div>
				</div>
				</div>
			</div>
			<style type="text/css">

			</style>
@stop