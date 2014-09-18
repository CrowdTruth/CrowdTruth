<div class="tab-pane" id="biographynet-sentence_tab">	
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
						<li><a href="#" data-vb="show" data-vbSelector="content_file_name"></i>File Name</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="content_chunk_name"></i>Chunk Name</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="content_chunk_text"></i>Text</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="number_of_batches"></i>Used In # of Batches</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="number_of_jobs"></i>Used In # of Jobs</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="created_at"></i>Created At</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="created_by"></i>Created By</a></li>
					</ul>
				</div>
			</div>
		</div>	
	</div>
	<div class='ctable-responsive cResults'>
	    <table class="table table-striped qwe">
	       	<thead data-query-key="match[documentType]" data-query-value="biographynet-sentence">
		        <tr>
		            <th data-vbIdentifier="checkbox">Select</th>
			    	<th class="sorting" data-vbIdentifier="content_file_name" data-query-key="orderBy[content.file_name]">File Name</th>
			    	<th class="sorting" data-vbIdentifier="content_chunk_name" data-query-key="orderBy[content.chunk_name]">Chunk Name </th>
			    	<th class="sorting" data-vbIdentifier="content_chunk_text" data-query-key="orderBy[content.chunk_text]">Text</th>
					<th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_batches" data-query-key="orderBy[cache.batches.count]">Used In # of Batches</th>
				    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_jobs" data-query-key="orderBy[cache.jobs.count]">Used In # of Jobs</th>     
				    <th class="sorting whiteSpaceNoWrap" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]" style="min-width:220px; width:auto;">Created At</th>
				    <th class="sorting" data-vbIdentifier="created_by" data-query-key="orderBy[user_id]">Created By</th>
		        </tr>
				<tr class="inputFilters">
					<td data-vbIdentifier="checkbox">
						<input type="checkbox" class="checkAll" />
					</td>
					<td data-vbIdentifier="content_file_name">
						<input class="input-sm form-control" type='text' data-query-key="match[content.file_name]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="content_chunk_name">
						<input class="input-sm form-control" type='text' data-query-key="match[content.chunk_name]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="content_chunk_text">
						<input class="input-sm form-control" type='text' data-query-key="match[content.chunk_text]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="number_of_batches">
						<input class="input-sm form-control" type='text' data-query-key="match[cache.batches.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[cache.batches.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
					</td>
					<td data-vbIdentifier="number_of_jobs">
						<input class="input-sm form-control" type='text' data-query-key="match[cache.jobs.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[cache.jobs.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
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
				    	<td data-vbIdentifier="content_file_name"> 
							@{{ this.content.file_name }}
				    	</td>
				    	<td data-vbIdentifier="content_chunk_name">
				    		<a class='testModal' id='@{{ this._id }}' data-modal-query="unit=@{{this._id}}" data-api-target="{{ URL::to('api/analytics/unit?') }}" data-target="#modalIndividualMetadataDescriptionUnit" data-toggle="tooltip" data-placement="top" title="Click to see the individual unit page">
			            		@{{ this.content.chunk_name }}
			            	</a>
			            </td>
			            <td data-vbIdentifier="content_chunk_text">
			            	@{{ this.content.chunk_text }}
			            </td>
				    	<td data-vbIdentifier="number_of_batches">@{{ this.cache.batches.count }}</td>
					    <td data-vbIdentifier="number_of_jobs">@{{ this.cache.jobs.count }}</td>
					    <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>
					    <td data-vbIdentifier="created_by">@{{ this.user_id }}</td>
					</tr>
			        @{{/each}}
				</script>
	        </tbody>
	    </table>
	</div>			
</div>