<!-- START preprocess/chang/nav -->   
						<ul class="nav nav-tabs">
							<li{{ (Request::is('preprocess/chang/info') ? ' class="active"' : '') }}>{{ link_to('preprocess/chang/info', "Info") }}</li>
							<li{{ (Request::is('preprocess/chang/actions') ? ' class="active"' : '') }}>{{ link_to('preprocess/chang/actions', "Actions") }}</li>
						</ul>
<!-- END preprocess/chang/nav   --> 