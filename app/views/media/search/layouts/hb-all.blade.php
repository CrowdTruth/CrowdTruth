<div class="tab-pane active" id="all_tab">
	
	<div class='ctable-responsive'>		
	    <table class="table table-striped">
	        <thead data-query-key="match[tags][]=unit&only[]=format&only[]=domain&only[]=documentType&only[]=created_at&only[]" data-query-value="user_id">
		        <tr>
		            <th data-vbIdentifier="checkbox">Select</th>
		            <th class="" data-query-key="">
		            	<input class="" type='text' id="col_1"/>
		            </th>
		            <th>
		            	<input class="" type='button' value='+' />
		            </th>
                </tr>

                <tr class="inputFilters">
					<td>
						<input type="checkbox" class="checkAll" />
					</td>
					<td>
						<input class="input-sm form-control" type='text' data-query-key="match[content.relation.original]" data-query-operator="like" id="col_1_search"/>
					</td>
				</tr>											        
	        </thead>
	        <tbody class='results'>											
				<script class='template' type="text/x-handlebars-template">
			        @{{#each documents}}
			        <tr>
			            <td data-vbIdentifier="checkbox"><input type="checkbox" id="@{{ this._id }}" name="rowchk" value="@{{ this._id }}"></td>
			            <td data-vbIdentifier="id">@{{ this._id }}</td>
			        </tr>
			        @{{/each}}
				</script>
	        </tbody>
	    </table>
    </div>											
</div>
<script>
	$("#col_1").change(function() {
		var searchColumn = this.val();
		var myId = this.id;
		$("#" + myId + "_search").attr("data-query-key", "match[" + searchColumn + "]");
		
	});
</script>