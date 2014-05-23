@extends('media.preprocess.relex.layouts.default')

@section('preprocessHeader')
	Relex <small> preview </small>
@stop

@section('colWidth', 'col-xs-12')
@section('relexContent')

	@include('media.view.text.layouts.relex_content')

@stop

@section('end_javascript')
	@yield('extra_js')
@stop