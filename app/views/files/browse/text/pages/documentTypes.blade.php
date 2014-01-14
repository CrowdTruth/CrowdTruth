@extends('layouts.default')

@section('head')
		{{ stylesheet_link_tag('custom/file.css') }}
@stop

@section('content')

				<!-- START files/browse/index --> 			
				<div class="col-xs-10 col-md-offset-1">
					<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')						
						<div class='row' style="margin-bottom:0px">
							<div class="col-xs-8">
								@include('files.layouts.nav')
							</div>
							<div class="col-xs-4 text-center">
								<h2 class='thumbHeader'>{{$domainType}} &nbsp;<small>document-types</small></h2>
							</div>
						</div>
						@include('files.browse.layouts.breadcrumb')

						<div class="row">
						@foreach($documentTypes as $documentType)
							<div class="col-xs-4">
								<a href="{{ URL::to('files/browse/text/' . $domainType . '/' . $documentType) }}" class="thumbnail">
									<img src="holder.js/100%x200/CW_1/text:{{ $documentType }}" />
								</a>
							</div>
						@endforeach
						</div>
					</div>
				</div>
				<!-- STOP files/browse/index --> 				
@stop

@section('end_javascript')
	<script> Holder.add_theme("CW_1", { background: "#B1D8C0", foreground: "white", size: 25 })</script>
	<script> Holder.add_theme("CW_2", { background: "#a9cbd1", foreground: "white", size: 25 })</script>
	<script> Holder.add_theme("CW_3", { background: "#d6afaf", foreground: "white", size: 25 })</script>
@stop