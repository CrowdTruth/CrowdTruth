<div class="tab-pane" id="termpairs-sentence_tab">	
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
						<li><a href="#" data-vb="show" data-vbSelector="content_class"></i>Class</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="content_focus"></i>Focus</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="content_candidate"></i>Candidate</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="content_passage1"></i>Passage 1</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="content_passage2"></i>Passage 2</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="created_at"></i>Created At</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="created_by"></i>Created By</a></li>
					</ul>
				</div>
			</div>
		</div>	
	</div>
	<div class='ctable-responsive cResults'>
	    <table class="table table-striped qwe">
	       	<thead data-query-key="match[documentType]" data-query-value="termpairs-sentence">
		        <tr>
		            <th data-vbIdentifier="checkbox">Select</th>
			    	<th class="sorting" data-vbIdentifier="content_class" data-query-key="orderBy[content.class]">Class</th>
			    	<th class="sorting" data-vbIdentifier="content_focus" data-query-key="orderBy[content.terms.focus]">Focus</th>
			    	<th class="sorting" data-vbIdentifier="content_candidate" data-query-key="orderBy[content.terms.candidate]">Candidate</th>
					<th class="sorting whiteSpaceNormal" data-vbIdentifier="content_passage1" data-query-key="orderBy[content.examples.passage1]">Passage 1</th>
				    <th class="sorting whiteSpaceNormal" data-vbIdentifier="content_passage2" data-query-key="orderBy[content.examples.passage2]">Passage 2</th>     
				    <th class="sorting whiteSpaceNoWrap" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]" style="min-width:220px; width:auto;">Created At</th>
				    <th class="sorting" data-vbIdentifier="created_by" data-query-key="orderBy[user_id]">Created By</th>
		        </tr>
				<tr class="inputFilters">
					<td data-vbIdentifier="checkbox">
						<input type="checkbox" class="checkAll" />
					</td>
					<td data-vbIdentifier="content_class">
						<input class="input-sm form-control" type='text' data-query-key="match[content.class]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="content_focus">
						<input class="input-sm form-control" type='text' data-query-key="match[content.terms.focus]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="content_candidate">
						<input class="input-sm form-control" type='text' data-query-key="match[content.terms.candidate]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="content_passage1">
						<input class="input-sm form-control" type='text' data-query-key="match[content.examples.passage1]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="content_passage2">
						<input class="input-sm form-control" type='text' data-query-key="match[content.examples.passage2]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="created_at">
						<div class="input-daterange">
						    <input type="text" class="input-sm form-control" name="start" data-query-key="match[created_at]" data-query-operator=">=" style="width:49% !important; float:left;" placeholder="Start Date" />
						    <input type="text" class="input-sm form-control" name="end" data-query-key="match[created_at]" data-query-operator="=<" style="width:49% !important; float:right;" placeholder="End Date" />
						</div>
					</td>
					<td data-vbIdentifier="created_by">
						<input class="input-sm form-control" type='text' data-query-key="match[user_id]" data-query-operator="like" />	
					</td>
				</tr>											        
	        </thead>
	        <tbody class='results'>								
				<script class='template' type="text/x-handlebars-template">
			        @{{#each documents}}
			        <tr class="text-center">
			            <td data-vbIdentifier="checkbox"><input type="checkbox" id="@{{ this._id }}" name="rowchk" value="@{{ this._id }}"></td>
				    	<td data-vbIdentifier="content_class"> 
							@{{ this.content.class }}
				    	</td>
				    	<td data-vbIdentifier="content_focus">
				    		<span class='highlight-t1'>
								@{{ this.content.terms.focus }}
			            	</span>
			            </td>
			            <td data-vbIdentifier="content_candidate">
				    		<span class='highlight-t2'>
								@{{ this.content.terms.candidate }}
							</span>
			            </td>
				    	<td data-vbIdentifier="content_passage1">@{{ highlight this.content.examples.passage1 }}</td>
					    <td data-vbIdentifier="content_passage2">@{{ highlight this.content.examples.passage2 }}</td>
					    <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>
					    <td data-vbIdentifier="created_by">@{{ this.user_id }}</td>
					</tr>
			        @{{/each}}
				</script>
	        </tbody>
	    </table>
	</div>			
</div>