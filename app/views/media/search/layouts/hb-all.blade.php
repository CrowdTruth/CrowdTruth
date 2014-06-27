<div class="tab-pane active" id="all_tab">
	<div class='row'>
		<div class='tabOptions hidden'>
			<div class='btn-group' style="margin-left:5px;">
				<button type="button" class="btn btn-default openAllColumns">Open all columns</button>
				<button type="button" class="btn btn-default openDefaultColumns hidden">Open default columns</button>
				<div class="btn-group vbColumns">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#" data-vb="show" data-vbSelector="id"></i>ID</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="format"></i>Format</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="domain"></i>Domain</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="domain"></i>Document-Type</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="created_at"></i>Created</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="useragent"></i>Agent</a></li>
					</ul>
				</div>
			</div>
		</div>	
	</div>
	<div class='ctable-responsive'>		
	    <table class="table table-striped">
	        <thead data-query-key="match[tags][]=unit&only[]=format&only[]=domain&only[]=documentType&only[]=created_at&only[]" data-query-value="user_id">
		        <tr>
		            <th data-vbIdentifier="checkbox">Select</th>
		            <th class="sorting" data-query-key="orderBy[_id]">ID</th>
		            <th class="sorting" data-query-key="orderBy[format]">Format</th>
		            <th class="sorting" data-query-key="orderBy[domain]">Domain</th>
		            <th class="sorting" data-query-key="orderBy[documentType]">Document-Type</th>
		            <th class="sorting" data-query-key="orderBy[created_at]">Created</th>
		            <th class="sorting" data-query-key="orderBy[user_id]">Agent</th>
		        </tr>
				<tr class="inputFilters">
					<td>
						<input type="checkbox" class="checkAll" />
					</td>
					<td>
						<input class="input-sm form-control" type='text' data-query-key="match[_id]" data-query-operator="like" />
					</td>
					<td>
						<input class="input-sm form-control" type='text' data-query-key="match[format]" data-query-operator="like" />
					</td>
					<td>
						<input class="input-sm form-control" type='text' data-query-key="match[domain]" data-query-operator="like" />
					</td>
					<td>
                        <input class="input-sm form-control" type='text' data-query-key="match[documentType]" data-query-operator="like" />
                    </td>
					<td data-vbIdentifier="created_at">
						<div class="input-daterange">
						    <input type="text" class="input-sm form-control" name="start" data-query-key="match[created_at]" data-query-operator=">=" style="width:49% !important; float:left;" placeholder="Start Date" />
						    <input type="text" class="input-sm form-control" name="end" data-query-key="match[created_at]" data-query-operator="=<" style="width:49% !important; float:right;" placeholder="End Date" />
						</div>
					</td>
					<td>
						<input class="input-sm form-control" type='text' data-query-key="match[user_id]" data-query-operator="like" />
					</td>																	
				</tr>											        
	        </thead>
	        <tbody class='results'>											
				<script class='template' type="text/x-handlebars-template">
			        @{{#each documents}}
			        <tr>
			            <td data-vbIdentifier="checkbox"><input type="checkbox" id="@{{ this._id }}" name="rowchk" value="@{{ this._id }}"></td>
			            <td data-vbIdentifier="id">@{{ this._id }}</td>
			            <td data-vbIdentifier="format">@{{ this.format }}</td>
			            <td data-vbIdentifier="domain">@{{ this.domain }}</td>
			            <td data-vbIdentifier="documentType">@{{ this.documentType }}</td>
			            <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>	
			            <td data-vbIdentifier="domain">@{{ this.user_id }}</td>			            		            			            
			        </tr>
			        @{{/each}}
				</script>
	        </tbody>
	    </table>
    </div>											
</div>