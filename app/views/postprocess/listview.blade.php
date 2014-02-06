@extends('layouts.default')

@section('content')
	<div id="container" class="container">
		<div class="row">
			
			<div  id="filtercolumn" class="col-md-2 ">

			<!-- Left column for sorting -->
			
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Sort by:</h3>
					</div>
					<div class="panel-body panel-nav-bar panel-nav-bar-active" id="completion" style="border-bottom: 1px solid #eee" onClick="sortModel('completion')">
						<i class="fa fa-check-circle"></i> Completion<br>
					</div>
					<div class="panel-body panel-nav-bar" id="cost" style="border-bottom: 1px solid #eee" onClick="sortModel('cost')">
						<i class="fa fa-dollar"></i> Total cost<br>
					</div>
					<div class="panel-body panel-nav-bar" id="runningtime" style="border-bottom: 1px solid #eee" onClick="sortModel('runningtime')">
						<i class="fa fa-clock-o"></i> Running time<br>
					</div>
					<div class="panel-body panel-nav-bar" id="flagged" style="border-bottom: 1px solid #eee" onClick="sortModel('flagged')">
						<i class="fa fa-flag"></i> Flagged workers<br>
					</div>
					<div class="panel-body panel-nav-bar" id="jobsize" onClick="sortModel('jobsize')">
						<i class="fa fa-gavel"></i> Job size<br>
					</div>
				</div>
			
			<!-- Left column for filters -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Apply filter:</h3>
					</div>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						<i class="fa fa-user"></i> {{Form::label('user', 'Created by:')}}<br>
						{{Form::input('','')}}
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
						More??
					</div>
				</div>
			<!-- END OF LEFT COLUMN HERE -->
			</div>


			<!-- Main column with results -->
			<div id="results">
			@include('postprocess.results')
			<!-- Close results column -->
			</div>


		<!-- Close row -->
		</div>
	<!-- Close container -->
	</div>
@section('end_javascript')
	
	<script>
		function showDetails(id){           
			$('#details-'+id).toggle(this.checked);
			}

		function sortModel(method){
			$.ajax({
				url: 'sort/' + method,
				type: 'GET'
			}).done(function( data ) {
            	console.log( data );
            	$("#results").html(data);

            	$(".panel-nav-bar").removeClass("panel-nav-bar-active");
            	$("#"+method).addClass("panel-nav-bar-active");
        	});


		}

		
			// ON HOLD: AUTOCOMPLETE FUNCTION WITH CREATEDBY

		// //autocomplete function for createdBy
		// $("#AccountNumber").autocomplete({
  //   	// This GET Request returns an Array of Objects used for Auto-Complete:
  //   	// [ { label: "Choice1", value: "value1" }, ... ]
  //   		source: '/api/Customers/AccountNumsAuto',
  //   		});

		// //METHOD WITH CACHING
		//  $(function() {
		// 	var cache = {};
		// 	$( "#createdBy" ).autocomplete({
		// 		minLength: 2,
		// 		source: function( request, response ) {
		// 					var term = request.term;
		// 					if ( term in cache ) {
		// 						response( cache[ term ] );
		// 						return;
		// 						}
		// 					$.getJSON( "createdBy.php", request, function( data, status, xhr ) {
		// 						cache[ term ] = data;
		// 						response( data );
		// 						});
		// 					}		
		// 	});
		// });

	</script>
@stop