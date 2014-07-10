@extends('media.preprocess.pages.index')

@section('preprocessPage')
	@include('media.preprocess.layouts.breadcrumb')
		<div class='tab'>
			@yield('metadatadescriptionContent')								
		</div>
@stop