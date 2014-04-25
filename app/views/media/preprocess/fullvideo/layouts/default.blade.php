@extends('media.preprocess.pages.index')

@section('preprocessPage')
	@include('media.preprocess.layouts.breadcrumb')
		<div class='tab'>
			@yield('fullvideoContent')								
		</div>
@stop