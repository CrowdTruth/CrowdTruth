<div class="tab-pane" id="twrex-structured-sentence_tab">
	<table class='table table-striped table-condensed datatable_options'>
		<tbody>
			<tr>
				<td>Relation In Sentence</td>
				<td>
					<div class="btn-group" id='relationInSentence'>
					  <button type="button" class="btn btn-sm btn-info" data-field-query="field[content.properties.relationInSentence]=1">With</button>
					  <button type="button" class="btn btn-sm btn-info" data-field-query="field[content.properties.relationInSentence]=0">Without</button>
					  <button type="button" class="btn btn-sm btn-success active">Not Applied</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Relation Outside Terms</td>
				<td>
					<div class="btn-group" id='relationOutsideTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-field-query="field[content.properties.relationOutsideTerms]=1">With</button>
					  <button type="button" class="btn btn-sm btn-info" data-field-query="field[content.properties.relationOutsideTerms]=0">Without</button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">Not Applied</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Relation Between Terms</td>
				<td>
					<div class="btn-group" id='relationBetweenTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-field-query="field[content.properties.relationBetweenTerms]=1">With</button>
					  <button type="button" class="btn btn-sm btn-info" data-field-query="field[content.properties.relationBetweenTerms]=0">Without</button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">Not Applied</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Semicolon Between Terms</td>
				<td>
					<div class="btn-group" id='semicolonBetweenTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-field-query="field[content.properties.semicolonBetweenTerms]=1">With</button>
					  <button type="button" class="btn btn-sm btn-info" data-field-query="field[content.properties.semicolonBetweenTerms]=0">Without</button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">Not Applied</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Comma-separated Sentence</td>
				<td>
					<div class="btn-group" id='commaSeparatedTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-field-query="field[content.properties.commaSeparatedTerms]=1">With</button>
					  <button type="button" class="btn btn-sm btn-info" data-field-query="field[content.properties.commaSeparatedTerms]=0">Without</button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">Not Applied</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Parenthesis Around Terms</td>
				<td>
					<div class="btn-group" id='parenthesisAroundTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-field-query="field[content.properties.parenthesisAroundTerms]=1">With</button>
					  <button type="button" class="btn btn-sm btn-info" data-field-query="field[content.properties.parenthesisAroundTerms]=0">Without</button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">Not Applied</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Overlapping Terms</td>
				<td>
					<div class="btn-group" id='overlappingTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-field-query="field[content.properties.overlappingTerms]=1">With</button>
					  <button type="button" class="btn btn-sm btn-info" data-field-query="field[content.properties.overlappingTerms]=0">Without</button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">Not Applied</button>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<div class='table-responsive'>
		<table class='table table-striped datatable_content'>
			<thead>
				<tr data-field-query="field[documentType][]=twrex-structured-sentence">
					<th data-key="content.relation.noPrefix">Relation</th>
					<th data-key="content.terms.first.text">Term 1</th>					
<!-- 					<th data-key="content.terms.first.startIndex">Term 1 StartIndex</th>
					<th data-key="content.terms.first.endIndex">Term 1 EndIndex</th> -->
					<th data-key="content.terms.second.text">Term 2</th>						
<!-- 					<th data-key="content.terms.second.startIndex">Term 2 Startindex</th>
					<th data-key="content.terms.second.endIndex">Term 2 EndIndex</th>		 -->			
					<th data-key="content.sentence.text">Sentence</th>
					<th data-key="content.properties.sentenceWordCount">Sentence Word Count</th>
				</tr>
			</thead>
			<tbody>
		    </tbody>
    	</table>
	</div>
</div>