<!-- START preprocess_breadcrumb -->   
						<ol class="breadcrumb">
							<li{{ (Request::is('preprocess/chang/*') ? ' class="active"' : '') }}>{{ link_to('preprocess/chang', "CHANG") }}</li>
							<li{{ (Request::segment(2) == 'something' ? ' class="active"' : '') }}>{{ link_to('files/browse', "Something") }}</li>
							<li{{ (Request::segment(2) == 'somethingelse' ? ' class="active"' : '') }}>{{ link_to('files/view', "Something else") }}</li>
						</ol>
<!-- END preprocess_breadcrumb   -->   