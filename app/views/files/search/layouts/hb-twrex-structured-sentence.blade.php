<div class="tab-pane" id="twrex-structured-sentence_tab">
	<div class='row'>
		<div class='searchOptions col-xs-12'>
			<select name="search_limit" class="selectpicker pull-left">
				<option value="10">10 Records per page</option>
				<option value="25">25 Records per page</option>
				<option value="50">50 Records per page</option>
				<option value="100">100 Records per page</option>
			</select>
			<div class='visibleColumns pull-left'>
			</div>		
		</div>
	</div>
	<div class='row'>
		<div class='col-xs-12 cw_pagination'>
		</div>								
	</div>
    <table class="table table-striped">
        <thead data-query-key="field[documentType]" data-query-value="twrex-structured-sentence">
	        <tr>
	            <th>Checkbox</th>
	            <th class="sorting" data-query-key="orderBy[content.relation.noPrefix]">Relation</th>
	            <th class="sorting" data-query-key="orderBy[content.terms.first.text]">Term 1</th>
	            <th class="sorting" data-query-key="orderBy[content.terms.second.text]">Term 2</th>
	            <th class="sorting" data-query-key="orderBy[content.sentence.text]">Sentence</th>
	        </tr>
			<tr class="inputFilters">
				<td>
					<input type="checkbox" class="checkAll" />
				</td>
				<td>
					<input type='text' data-query-key="field[content.relation.noPrefix]" data-query-operator="[like]" />
				</td>
				<td>
					<input type='text' data-query-key="field[content.terms.first.text]" data-query-operator="[like]" />
				</td>
				<td>
					<input type='text' data-query-key="field[content.terms.second.text]" data-query-operator="[like]" />
				</td>
				<td>
					<input type='text' data-query-key="field[content.sentence.text]" data-query-operator="[like]" />
				</td>
			</tr>											        
        </thead>
        <tbody class='results'>											
			<script class='template' type="text/x-handlebars-template">
		        @{{#each documents}}
		        <tr>
		            <td>Checkbox</td>
		            <td>@{{ this.content.relation.noPrefix }}</td>
		            <td>@{{ this.content.terms.first.text }}</td>
		            <td>@{{ this.content.terms.second.text }}</td>
		            <td>@{{ this.content.sentence.text }}</td>
		        </tr>
		        @{{/each}}
			</script>
        </tbody>
    </table>											
</div>