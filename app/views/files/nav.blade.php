<!-- START file_nav -->   
						<ul class="nav nav-tabs">
							<li{{ (Request::segment(2) == 'upload' ? ' class="active"' : '') }}>{{ link_to('files/upload', "Upload Files") }}</li>
							<li{{ (Request::segment(2) == 'browse' ? ' class="active"' : '') }}>{{ link_to('files/browse', "Browse Files") }}</li>
							<li{{ (Request::segment(2) == 'view' ? ' class="active"' : '') }}>{{ link_to('#', "&nbsp;View&nbsp;") }}</li>
						</ul>
<!-- END file_nav   -->   