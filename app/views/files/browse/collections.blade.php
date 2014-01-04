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
								@include('files.nav')
							</div>
							<div class="col-xs-4 text-center">
								<h2 class='thumbHeader'>collection &nbsp;<small>types</small></h2>
							</div>
						</div>
						@include('files.browse.breadcrumb')

						<div class="row">
							<div class="col-xs-4">
								<a href="{{ URL::to('files/browse/text/') }}" class="thumbnail">
									<img src="holder.js/100%x200/CW_1/text:Text" />
								</a>
							</div>
							<div class="col-xs-4">
								<a href="#" class="thumbnail disabled">
									<img src="holder.js/100%x200/CW_2/text:Images" />
								</a>
							</div>
							<div class="col-xs-4">
								<a href="#" class="thumbnail disabled">
									<img src="holder.js/100%x200/CW_3/text:Videos" />
								</a>
							</div>
						</div>
					</div>
				</div>
				<!-- STOP files/browse/index --> 				
@stop

@section('end_javascript')
	{{ javascript_include_tag('jquery.tablesorter.min.js') }}
	{{ javascript_include_tag('jquery.tablesorter.widgets.min.js') }}

	<script type="text/javascript">
		$(document).ready(function () {

			$(".table").tablesorter({
				theme : 'bootstrap',
				stringTo: "max",

				// initialize zebra and filter widgets
				widgets: ["filter"],

				widgetOptions: {
				// include child row content while filtering, if true
				filter_childRows  : false,
				// class name applied to filter row and each input
				filter_cssFilter  : 'tablesorter-filter',
				// search from beginning
				filter_startsWith : false,
				// Set this option to false to make the searches case sensitive 
				filter_ignoreCase : true
				}
			});

			$('table').on("click", ".delete_file", function() {
				var _this = $(this);
				var jqxhr = $.post($(this).attr('href'));
				jqxhr.done(function(data) {
					$(_this).closest('tr').fadeOut();
					$('.menu_selection').empty().prepend($(data).html());
					console.log(data);
				});

				jqxhr.fail(function(data) {
					//  alert( data );
					console.log('fail');
					//  console.log(data);
				});

					return false;
			});			
		});
	</script>
	<script> Holder.add_theme("CW_1", { background: "#B1D8C0", foreground: "white", size: 25 })</script>
	<script> Holder.add_theme("CW_2", { background: "#a9cbd1", foreground: "white", size: 25 })</script>
	<script> Holder.add_theme("CW_3", { background: "#d6afaf", foreground: "white", size: 25 })</script>
@stop