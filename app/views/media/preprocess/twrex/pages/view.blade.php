@extends('media.preprocess.twrex.layouts.default')

@section('preprocessHeader')
	Twrex <small> preview </small>
@stop

@section('colWidth', 'col-xs-12')
@section('twrexContent')

	@include('media.view.text.layouts.twrex_content')

@stop

@section('end_javascript')
	@yield('extra_js')
@stop