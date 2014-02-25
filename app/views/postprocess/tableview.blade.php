@extends('layouts.default')
@section('head')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js"></script>
<script src="//cdn.jsdelivr.net/lodash/2.4.1/lodash.underscore.min.js"></script>
<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/restangular/1.3.1/restangular.min.js"></script>
<!-- <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/angularjs/1.0.6/angular-resource.min.js"></script> -->
@stop

@section('content')
	<div class="container" ng-app="dataRetrieval">
		<div class"row">
			<div class="col-md-10">
				<div ng-controller="restangularCtrl">
					@{{job}}
				</div>
				
				<div></div>
			</div>
		</div>
	</div>

@stop

@section('end_javascript')
<script>

</script>

@stop

