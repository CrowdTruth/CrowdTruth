<!-- START file_nav -->   
						<ul class="nav nav-tabs">
							<li{{ (Request::segment(2) == 'selectfile' ? ' class="active"' : '') }}>{{ link_to('process/selectfile', "Select file") }}</li>
							<li{{ (Request::segment(2) == 'template' ? ' class="active"' : '') }}>{{ link_to('process/template', "Pick/build Template") }}</li>
							<li{{ (Request::segment(2) == 'details' ? ' class="active"' : '') }}>{{ link_to('process/details', "Job Details") }}</li>
							<li{{ (Request::segment(2) == 'platform' ? ' class="active"' : '') }}>{{ link_to('process/platform', "Submit") }}</li>
							<li{{ (Request::segment(2) == 'amt' ? ' class="active"' : '') }}>{{ link_to('process/amt', "AMT") }}</li>
						</ul>
<!-- END file_nav   -->   