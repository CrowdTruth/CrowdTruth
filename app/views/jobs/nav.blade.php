
<!-- START process_nav -->   
						<ul class="nav nav-tabs" id="postprocesstabs">
							<li{{ (Request::segment(2) == 'listview' ? ' class="active"' : '') }} title='listview'>{{ link_to('postprocess/listview', "List") }}</li>
							<li{{ (Request::segment(2) == 'tableview' ? ' class="active"' : '') }} title='tableview'>{{ link_to('postprocess/tableview', "Table") }}</li>
						</ul>


<!-- END process_nav   -->   

