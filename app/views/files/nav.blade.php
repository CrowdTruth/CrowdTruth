<!-- START file_nav -->   
						<ul class="nav nav-tabs">
							<li{{ (Request::is('files/upload') ? ' class="active"' : '') }}>{{ link_to('files/upload', "Upload Files") }}</li>
							<li{{ (Request::is('files/browse') ? ' class="active"' : '') }}>{{ link_to('files/browse', "Browse Files") }}</li>
							<li{{ (Request::is('files/mongo') ? ' class="active"' : '') }}>{{ link_to('files/mongo', "Mongo test") }}</li>
						</ul>
<!-- END file_nav   -->   