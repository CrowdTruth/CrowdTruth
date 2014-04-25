<div class="tab-pane" id="twrex-structured-sentence_tab">
	<div class='row'>
		<div class='searchOptions col-xs-12'>
			<div class="btn-group pull-left">
				<button type="submit" class="btn btn-danger createBatchButton">Save selection</button>
				<a href='#' class="btn btn-warning toCSV">Export results to CSV</a>	
			</div>
			<div class="btn-group pull-right vbColumns" style="margin-left:5px;">
				<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
				Visible Columns <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="#" data-vb="show" data-vbSelector="checkbox"></i>Select</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="relation"></i>Relation</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="term_1"></i>Term 1</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="term_1_processed"></i>Term 1 Processed</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="term_2"></i>Term 2</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="term_2_processed"></i>Term 2 Processed</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="sentence"></i>Sentence</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="sentence_processed"></i>Processed Sentence</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="number_of_batches"></i>Used In # of Batches</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="number_of_jobs"></i>Used In # of Jobs</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="sentence_wordcount"></i>Sentence Word Count</a></li>					
					<li><a href="#" data-vb="show" data-vbSelector="created_at"></i>Created At</a></li>
				</ul>
			</div>
			<select name="search_limit" class="selectpicker pull-right">
				<option value="10">10 Records per page</option>
				<option value="25">25 Records per page</option>
				<option value="50">50 Records per page</option>
				<option value="100">100 Records per page</option>
				<option value="1000">1000 Records per page</option>
			</select>
		</div>
	</div>
	<div class='row'>
		<div class='col-xs-12'>
			<div class="btn-group pull-left searchStats">
			</div>
			<div class='cw_pagination text-right'>
			</div>
		</div>		
	</div>
	<table class='table table-striped table-condensed specificFilterOptions'>
		<tbody>
			<tr>
				<td>Relation In Sentence</td>
				<td class="text-right">
					<div class="btn-group" id='relationInSentence'>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="match[content.properties.relationInSentence]" data-query-value="1"><i class="fa fa-check"></i></button>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="match[content.properties.relationInSentence]" data-query-value="0"><i class="fa fa-minus"></i></button>
					  <button type="button" class="btn btn-sm btn-success active">N/A</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Relation Outside Terms</td>
				<td class="text-right">
					<div class="btn-group" id='relationOutsideTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="match[content.properties.relationOutsideTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="match[content.propertimes.relationOutsideTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">N/A</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Relation Between Terms</td>
				<td class="text-right">
					<div class="btn-group" id='relationBetweenTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="match[content.properties.relationBetweenTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="match[content.properties.relationBetweenTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">N/A</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Semicolon Between Terms</td>
				<td class="text-right">
					<div class="btn-group" id='semicolonBetweenTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="match[content.properties.semicolonBetweenTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="match[content.properties.semicolonBetweenTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">N/A</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Comma-separated Terms</td>
				<td class="text-right">
					<div class="btn-group" id='commaSeparatedTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="match[content.properties.commaSeparatedTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="match[content.properties.commaSeparatedTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">N/A</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Parenthesis Around Terms</td>
				<td class="text-right">
					<div class="btn-group" id='parenthesisAroundTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="match[content.properties.parenthesisAroundTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="match[content.properties.parenthesisAroundTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">N/A</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>Overlapping Terms</td>
				<td class="text-right">
					<div class="btn-group" id='overlappingTerms'>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="match[content.properties.overlappingTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
					  <button type="button" class="btn btn-sm btn-info" data-query-key="match[content.properties.overlappingTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
					  <button type="button" class="btn btn-sm btn-success active twrexNone">N/A</button>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<div class='table-responsive'>
	    <table class="table table-striped">
	        <thead data-query-key="match[documentType]" data-query-value="twrex-structured-sentence">
		        <tr>
		            <th data-vbIdentifier="checkbox">Select</th>
		            <th class="sorting" data-vbIdentifier="relation" data-query-key="orderBy[content.relation.noPrefix]">Relation</th>
		            <th class="sorting" data-vbIdentifier="term_1" data-query-key="orderBy[content.terms.first.text]">Term 1</th>
		            <th class="sorting" data-vbIdentifier="term_1_processed" data-query-key="orderBy[content.terms.first.formatted]">Term 1 Processed</th>
		            <th class="sorting" data-vbIdentifier="term_2" data-query-key="orderBy[content.terms.second.text]">Term 2</th>
		            <th class="sorting" data-vbIdentifier="term_2_processed" data-query-key="orderBy[content.terms.second.formatted]">Term 2 Processed</th>
		            <th class="sorting" data-vbIdentifier="sentence" data-query-key="orderBy[content.sentence.text]">Sentence</th>
		            <th class="sorting" data-vbIdentifier="sentence_processed" data-query-key="orderBy[content.sentence.formatted]">Processed Sentence</th>
		            <th class="sorting whiteSpaceNormal" data-vbIdentifier="sentence_wordcount" data-query-key="orderBy[content.properties.sentenceWordCount]">Sentence Word Count</th>
		            <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_batches" data-query-key="orderBy[cache.batches.count]">Used In # of Batches</th>
		            <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_jobs" data-query-key="orderBy[cache.jobs.count]">Used In # of Jobs</th>     
		            <th class="sorting whiteSpaceNoWrap" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]" style="min-width:220px; width:auto;">Created At</th>
		        </tr>
				<tr class="inputFilters">
					<td data-vbIdentifier="checkbox">
						<input type="checkbox" class="checkAll" />
					</td>
					<td data-vbIdentifier="relation">
						<input class="input-sm form-control" type='text' data-query-key="match[content.relation.noPrefix]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="term_1">
						<input class="input-sm form-control" type='text' data-query-key="match[content.terms.first.text]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="term_1_processed">
						<input class="input-sm form-control" type='text' data-query-key="match[content.terms.first.formatted]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="term_2">
						<input class="input-sm form-control" type='text' data-query-key="match[content.terms.second.text]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="term_2_processed">
						<input class="input-sm form-control" type='text' data-query-key="match[content.terms.second.formatted]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="sentence">
						<input class="input-sm form-control" type='text' data-query-key="match[content.sentence.text]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="sentence_processed">
						<input class="input-sm form-control" type='text' data-query-key="match[content.sentence.formatted]" data-query-operator="like" placeholder="Enter your search keywords here" />
					</td>
					<td data-vbIdentifier="sentence_wordcount">
						<input class="input-sm form-control" type='text' data-query-key="match[content.properties.sentenceWordCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[content.properties.sentenceWordCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
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
				</tr>											        
	        </thead>
	        <tbody class='results'>											
				<script class='template' type="text/x-handlebars-template">
			        @{{#each documents}}
			        <tr  class="text-center">
			            <td data-vbIdentifier="checkbox"><input type="checkbox" id="@{{ this._id }}" name="rowchk" value="@{{ this._id }}"></td>
			            <td data-vbIdentifier="relation">@{{ this.content.relation.noPrefix }}</td>
			            <td data-vbIdentifier="term_1">@{{ this.content.terms.first.text }}</td>
			            <td data-vbIdentifier="term_1_processed">@{{ this.content.terms.first.formatted }}</td>
			            <td data-vbIdentifier="term_2">@{{ this.content.terms.second.text }}</td>
			            <td data-vbIdentifier="term_2_processed">@{{ this.content.terms.second.formatted }}</td>
			            <td data-vbIdentifier="sentence" class="text-left">@{{ this.content.sentence.text }}</td>
			            <td data-vbIdentifier="sentence_processed" class="text-left">@{{ highlightTerms ../searchQuery this.content }}</td>
			            <td data-vbIdentifier="sentence_wordcount">@{{ this.content.properties.sentenceWordCount }}</td>
			            <td data-vbIdentifier="number_of_batches">@{{ this.cache.batches.count }}</td>
			            <td data-vbIdentifier="number_of_jobs">@{{ this.cache.jobs.count }}</td>
			            <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>
			        </tr>
			        @{{/each}}
				</script>
	        </tbody>
    	</table>
    </div>
</div>