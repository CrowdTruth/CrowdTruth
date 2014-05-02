<!-- START file_nav -->   
<!-- 						<ul class="nav nav-tabs">
							<li{{ (Request::segment(2) == 'upload' ? ' class="active"' : '') }}>{{ link_to('media/upload', "Upload Media") }}</li>
							<li{{ (Request::segment(2) == 'search' ? ' class="active"' : '') }}>{{ link_to('media/search', "Search Media") }}</li>
							<li{{ (Request::segment(2) == 'preprocess' ? ' class="active"' : '') }}>{{ link_to('media/preprocess', "Pre-Process Media") }}</li>
						</ul> -->
						<div class='modalFacade'>
							<h3 class='pageHeader'> @yield('pageHeader', 'Media') </h3>
							<a href='{{ URL::to('media/search') }}' class='toSearch'><span class="glyphicon glyphicon glyphicon-remove"></span></a>
						</div>
<!-- END file_nav   --> 