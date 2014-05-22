<div class="tab-pane" id="relex-structured-sentence_tab">
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
				<td>Comma-separated Sentence</td>
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
	<div class='table-responsive'>
		<table class='table table-striped datatable_content'>
			<thead>
				<tr data-query-key="field[documentType]" data-query-value="relex-structured-sentence">
					<th data-column="content.relation.noPrefix">Relation</th>
					<th data-column="content.terms.first.text">Term 1</th>					
					<th data-column="content.terms.first.startIndex" data-bvisible="false">Term 1 StartIndex</th>
					<th data-column="content.terms.first.endIndex" data-bvisible="false">Term 1 EndIndex</th>
					<th data-column="content.terms.second.text">Term 2</th>
					<th data-column="content.terms.second.startIndex" data-bvisible="false">Term 2 StartIndex</th>
					<th data-column="content.terms.second.endIndex" data-bvisible="false">Term 2 EndIndex</th>

<!-- 					<th data-column="content.terms.second.startIndex">Term 2 Startindex</th>
					<th data-column="content.terms.second.endIndex">Term 2 EndIndex</th>		 -->			
					<th data-column="content.sentence.text">Sentence</th>
					<th data-column="content.properties.sentenceWordCount">Sentence Word Count</th>
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
						<input type='text' data-query-key="field[content.terms.first.startIndex]" data-query-operator="[>]" style="width:49%; float:left;" placeholder="gt" />
						<input type='text' data-query-key="field[content.terms.first.startIndex]" data-query-operator="[<]" style="width:49%; float:right;" placeholder="lt" />
					</td>
					<td>
						<input type='text' data-query-key="field[content.terms.first.endIndex]" data-query-operator="[>]" style="width:49%; float:left;" placeholder="gt" />
						<input type='text' data-query-key="field[content.terms.first.endIndex]" data-query-operator="[<]" style="width:49%; float:right;" placeholder="lt" />
					</td>
					<td>
						<input type='text' data-query-key="field[content.terms.second.text]" data-query-operator="[like]" />
					</td>
					<td>
						<input type='text' data-query-key="field[content.terms.second.startIndex]" data-query-operator="[>]" style="width:49%; float:left;" placeholder="aaa" />
						<input type='text' data-query-key="field[content.terms.second.startIndex]" data-query-operator="[<]" style="width:49%; float:right;" placeholder="lbbbt" />
					</td>
					<td>
						<input type='text' data-query-key="field[content.terms.second.endIndex]" data-query-operator="[>]" style="width:49%; float:left;" placeholder="gt" />
						<input type='text' data-query-key="field[content.terms.second.endIndex]" data-query-operator="[<]" style="width:49%; float:right;" placeholder="lt" />
					</td>
					<td>
						<input type='text' data-query-key="field[content.sentence.text]" data-query-operator="[like]" />
					</td>
					<td>
						<input type='text' data-query-key="field[content.properties.sentenceWordCount]" data-query-operator="[>]" style="width:49%; float:left;" placeholder="gt" />
						<input type='text' data-query-key="field[content.properties.sentenceWordCount]" data-query-operator="[<]" style="width:49%; float:right;" placeholder="lt" />
					</td>
				</tr>
			</thead>
			<tbody>
		    </tbody>
    	</table>
	</div>
</div>