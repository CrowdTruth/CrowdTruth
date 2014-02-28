@extends('layouts.default')

@section('head')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js"></script>
<script src="//cdn.jsdelivr.net/lodash/2.4.1/lodash.underscore.min.js"></script>
<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/restangular/1.3.1/restangular.min.js"></script>
<script type="text/javascript" src="http://code.angularjs.org/1.2.9/angular-resource.min.js"></script>
@stop


@section('content')
			<div  id="filtercolumn" class="col-md-2 ">
			<!-- Left column for sorting -->

				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Sort by:</h3>
					</div>
					
					 <!-- Sorting functionality works by checking the css class of the div, and on click it renders the results again.
					 onClick the method of sorting is passed as a parameter. It is important however, that the div has the same name as the 
					 parameter for the js to function properly -->

					<div class="panel-body panel-nav-bar panel-nav-bar-ascending" id="judgmentsPerUnit" style="border-bottom: 1px solid #eee" onClick="sortModel('judgmentsPerUnit')">
						<i class="fa fa-check-circle"></i> Completion<br>
					</div>
					<div class="panel-body panel-nav-bar" id="totalCost" style="border-bottom: 1px solid #eee" onClick="sortModel('totalCost')">
						<i class="fa fa-dollar"></i> Total cost<br>
					</div>
					<div class="panel-body panel-nav-bar" id="created_at" style="border-bottom: 1px solid #eee" onClick="sortModel('created_at')">
						<i class="fa fa-clock-o"></i> Running time<br>
					</div>
					<div class="panel-body panel-nav-bar" id="flaggedWorkers" style="border-bottom: 1px solid #eee" onClick="sortModel('flaggedWorkers')">
						<i class="fa fa-flag"></i> Flagged workers<br>
					</div>
					<div class="panel-body panel-nav-bar" id="jobSize" onClick="sortModel('jobSize')">
						<i class="fa fa-gavel"></i> Job size<br>
					</div>
				</div>
			
			<!-- Left column for filters -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Apply filter:</h3>
					</div>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						<i class="fa fa-user"></i> {{Form::label('createdBy', 'Created by:')}}<br>
						{{Form::input('createdBy','createdBy')}}
					</div>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						<i class="fa fa-users"></i> {{Form::label('user', 'Platform:')}}<br>
						{{Form::checkbox('')}} CrowdFlower<br>
						{{Form::checkbox('')}} Amazon MTurk
					</div>
					<seperator/>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						<i class="fa fa-file"></i> {{Form::label('user', 'Template:')}}<br>
						{{Form::checkbox('')}} Relation Direction<br>
						{{Form::checkbox('')}} Relation Extraction<br>
						{{Form::checkbox('')}} Factor Span
					</div>
					<div class="panel-body">
						Domain, Type, Status (Running, Completed)
					</div>
				</div>
			<!-- END OF LEFT COLUMN HERE -->
			</div>


			<!-- Main column with results -->
			<div id="results">
			@include('postprocess.results')
			<!-- Close results column -->
			</div>

@section('end_javascript')
	
	<script>
		function showDetails(id){           
			$('#details-'+id).toggle(this.checked);
			}

		function sortModel(method){
			// checks whether current method is on descend or ascend and stores it in variable
			var sort = 'none';
			if($("#"+method).hasClass("panel-nav-bar-ascending"))
				{sort = 'desc';}
			if($('#'+method).hasClass("panel-nav-bar-descending"))
				{sort = 'asc';}
			// ajax request with the method of sorting and the ascend/descend option, response is the result view
			$.ajax({
				url: 'sort/' + method + "/" + sort,
				type: 'GET'
			}).done(function( data ) {
				// put the response in the results div
               	$("#results").html(data);
               	// reset the styling of the sorting panel
            	$(".panel-nav-bar").removeClass("panel-nav-bar-ascending panel-nav-bar-descending");
            	// set the styling on the sorting panel and remember whether it is descending or ascending
            	if (sort == "desc")
					{$("#"+method).addClass("panel-nav-bar-descending");}				
	       		else {$("#"+method).addClass("panel-nav-bar-ascending");}
        	});
		}

		
	</script>
@stop