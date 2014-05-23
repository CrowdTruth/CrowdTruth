@extends('preprocess.relex.layouts.default')

@section('colWidth', 'col-xs-12')
@section('relexContent')

@include('files.view.text.layouts.relex_content')

@stop

@section('end_javascript')
	@yield('extra_js')
@stop
