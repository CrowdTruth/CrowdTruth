<!DOCTYPE html>
<html lang="en" ng-app="crowdWatson">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Crowd-Watson</title>
		<!--<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>-->
		{{ stylesheet_link_tag() }}
			
@yield('head')
	</head>
	<body>
		@include('layouts.navbar')
		
		<div class="
		@yield('container', 'container') 
		CW_{{Request::segment(2)}}">
			<div class="row">
@yield('content')
			</div>   
		</div>
	</div>

@yield('modal')	
	{{ javascript_include_tag() }}
<script type="text/javascript">
	$(document).ready(function () {
		$(document).bind('click', function(e) {
			if(!$(e.target).is('.btn') && !$(e.target).is('.popover *')) {
				$('.selectionButton').popover("hide");
			}
		});

		$('body').on( "click", ".update_selection", function() {
			var _this = this;
			var href = $(this).attr('href');
			var jqxhr = $.post(href);
			jqxhr.done(function(data) {
				// alert( data );
				// console.log('done');
				console.log(data);
				$(_this).closest('.btn-group').find('.dropdown-toggle').click();

				if (href.toLowerCase().indexOf("destroy") >= 0){
					$('.tableSelection').fadeOut();
				}

				if (href.toLowerCase().indexOf("remove") >= 0){
					$('a[href="' + href + '"]').closest('tr').remove();
				} else {
					$(".selectionButton").popover("show");
				}

				$('.menu_selection').empty().prepend($(data).html());
			});

			jqxhr.fail(function(data) {
				//  alert( data );
				console.log('fail');
				//  console.log(data);
			});

			return false;
		});

		$('table').on("click", ".delete_document", function() {

			if(confirm("Are you sure you want to delete this document?")) {
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

			}

			return false;

		});			
	});
</script>
@yield('end_javascript')
@yield('platformend')
@yield('selection_user_javascript')
	</body>
</html>