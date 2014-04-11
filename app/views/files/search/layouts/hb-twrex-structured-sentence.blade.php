<div class="tab-pane" id="twrex-structured-sentence_tab">
	<div class='row'>
		<div class='searchOptions col-xs-12'>
			<select name="search_limit" class="selectpicker pull-left">
				<option value="10">10 Records per page</option>
				<option value="25">25 Records per page</option>
				<option value="50">50 Records per page</option>
				<option value="100">100 Records per page</option>
				<option value="1000">1000 Records per page</option>
			</select>
			<div class="btn-group pull-right vbColumns">
				<a href='#' class="btn btn-warning toCSV">Export results to CSV</a>				
				<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
				Visible Columns <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="#" data-vb="hide" data-vbSelector="checkbox"></i>Checkbox</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="relation"></i>Relation</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="term_1"></i>Term 1</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="term_1_formatted"></i>Term 1 Formatted</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="term_2"></i>Term 2</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="term_2_formatted"></i>Term 2 Formatted</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="sentence"></i>Sentence</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="sentence_formatted"></i>Formatted Sentence</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="sentence_wordcount"></i>Sentence Word Count</a></li>
				</ul>
			</div>				
		</div>
	</div>
	<div class='row'>
		<div class='col-xs-12 cw_pagination'>
		</div>				
	</div>
	<table class='table table-striped table-condensed datatable_options'>
		<tbody>
			<tr>
				<td>Relation In Sentence</td>
				<td>
					<div class="btn-group" id='relationInSentence'>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="field[content.properties.relationInSentence]" data-query-value="1">With</button>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="field[content.properties.relationInSentence]" data-query-value="0">Without</button>
					  <button type="button" class="btn btn-sm btn-success active">Not Applied</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Relation Outside Terms</td>
				<td>
					<div class="btn-group" id='relationOutsideTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="field[content.properties.relationOutsideTerms]" data-query-value="1">With</button>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="field[content.properties.relationOutsideTerms]" data-query-value="0">Without</button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">Not Applied</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Relation Between Terms</td>
				<td>
					<div class="btn-group" id='relationBetweenTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="field[content.properties.relationBetweenTerms]" data-query-value="1">With</button>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="field[content.properties.relationBetweenTerms]" data-query-value="0">Without</button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">Not Applied</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Semicolon Between Terms</td>
				<td>
					<div class="btn-group" id='semicolonBetweenTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="field[content.properties.semicolonBetweenTerms]" data-query-value="1">With</button>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="field[content.properties.semicolonBetweenTerms]" data-query-value="0">Without</button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">Not Applied</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Comma-separated Terms</td>
				<td>
					<div class="btn-group" id='commaSeparatedTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="field[content.properties.commaSeparatedTerms]" data-query-value="1">With</button>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="field[content.properties.commaSeparatedTerms]" data-query-value="0">Without</button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">Not Applied</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Parenthesis Around Terms</td>
				<td>
					<div class="btn-group" id='parenthesisAroundTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="field[content.properties.parenthesisAroundTerms]" data-query-value="1">With</button>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="field[content.properties.parenthesisAroundTerms]" data-query-value="0">Without</button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">Not Applied</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Overlapping Terms</td>
				<td>
					<div class="btn-group" id='overlappingTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="field[content.properties.overlappingTerms]" data-query-value="1">With</button>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="field[content.properties.overlappingTerms]" data-query-value="0">Without</button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">Not Applied</button>
					</div>
				</td>
			</tr>
		</tbody>
	</table>	
    <table class="table table-striped">
        <thead data-query-key="field[documentType]" data-query-value="twrex-structured-sentence">
	        <tr>
	            <th data-vbIdentifier="checkbox">Checkbox</th>
	            <th class="sorting" data-vbIdentifier="relation" data-query-key="orderBy[content.relation.noPrefix]">Relation</th>
	            <th class="sorting" data-vbIdentifier="term_1" data-query-key="orderBy[content.terms.first.text]">Term 1</th>
	            <th class="sorting" data-vbIdentifier="term_1_formatted" data-query-key="orderBy[content.terms.first.formatted]">Term 1 Formatted</th>
	            <th class="sorting" data-vbIdentifier="term_2" data-query-key="orderBy[content.terms.second.text]">Term 2</th>
	            <th class="sorting" data-vbIdentifier="term_2_formatted" data-query-key="orderBy[content.terms.second.formatted]">Term 2 Formatted</th>
	            <th class="sorting" data-vbIdentifier="sentence" data-query-key="orderBy[content.sentence.text]">Sentence</th>
	            <th class="sorting" data-vbIdentifier="sentence_formatted" data-query-key="orderBy[content.sentence.formatted]">Formatted Sentence</th>
	            <th class="sorting whiteSpaceNormal" data-vbIdentifier="sentence_wordcount" data-query-key="orderBy[content.properties.sentenceWordCount]">Sentence Word Count</th>
	        </tr>
			<tr class="inputFilters">
				<td data-vbIdentifier="checkbox">
					<input type="checkbox" class="checkAll" />
				</td>
				<td data-vbIdentifier="relation">
					<input type='text' data-query-key="field[content.relation.noPrefix]" data-query-operator="[like]" />
				</td>
				<td data-vbIdentifier="term_1">
					<input type='text' data-query-key="field[content.terms.first.text]" data-query-operator="[like]" />
				</td>
				<td data-vbIdentifier="term_1_formatted">
					<input type='text' data-query-key="field[content.terms.first.formatted]" data-query-operator="[like]" />
				</td>
				<td data-vbIdentifier="term_2">
					<input type='text' data-query-key="field[content.terms.second.text]" data-query-operator="[like]" />
				</td>
				<td data-vbIdentifier="term_2_formatted">
					<input type='text' data-query-key="field[content.terms.second.formatted]" data-query-operator="[like]" />
				</td>
				<td data-vbIdentifier="sentence">
					<input type='text' data-query-key="field[content.sentence.text]" data-query-operator="[like]" />
				</td>
				<td data-vbIdentifier="sentence_formatted">
					<input type='text' data-query-key="field[content.sentence.formatted]" data-query-operator="[like]" />
				</td>
				<td data-vbIdentifier="sentence_wordcount">
					<input type='text' data-query-key="field[content.properties.sentenceWordCount]" data-query-operator="[>]" style="width:49%; float:left;" placeholder="gt" />
					<input type='text' data-query-key="field[content.properties.sentenceWordCount]" data-query-operator="[<]" style="width:49%; float:right;" placeholder="lt" />
				</td>
			</tr>											        
        </thead>
        <tbody class='results'>											
			<script class='template' type="text/x-handlebars-template">
		        @{{#each documents}}
		        <tr  class="text-center">
		            <td data-vbIdentifier="checkbox">Checkbox</td>
		            <td data-vbIdentifier="relation">@{{ this.content.relation.noPrefix }}</td>
		            <td data-vbIdentifier="term_1">@{{ this.content.terms.first.text }}</td>
		            <td data-vbIdentifier="term_1_formatted">@{{ this.content.terms.first.formatted }}</td>
		            <td data-vbIdentifier="term_2">@{{ this.content.terms.second.text }}</td>
		            <td data-vbIdentifier="term_2_formatted">@{{ this.content.terms.second.formatted }}</td>
		            <td data-vbIdentifier="sentence" class="text-left">@{{ this.content.sentence.text }}</td>
		            <td data-vbIdentifier="sentence_formatted" class="text-left">@{{ highlightTerms ../searchQuery this.content }}</td>
		            <td data-vbIdentifier="sentence_wordcount">@{{ this.content.properties.sentenceWordCount }}</td>
		        </tr>
		        @{{/each}}
			</script>
        </tbody>
    </table>											
</div>