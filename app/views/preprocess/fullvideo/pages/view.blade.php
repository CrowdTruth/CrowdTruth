@extends('preprocess.twrex.layouts.default')

@section('colWidth', 'col-xs-12')
@section('twrexContent')

@include('files.view.text.layouts.twrex_content')

@stop

@section('end_javascript')
	@yield('extra_js')
@stop
