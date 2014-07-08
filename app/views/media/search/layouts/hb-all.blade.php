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
                        <li><a href="#" data-vb="hide" data-vbSelector="number_of_cf_judgements"></i># CF judgements</a></li>
                        <li><a href="#" data-vb="hide" data-vbSelector="number_of_amt_judgements"></i># AMT judgements</a></li>
                        <li><a href="#" data-vb="hide" data-vbSelector="number_of_RelEx_jobs"></i># of RelEx jobs</a></li>
                        <li><a href="#" data-vb="hide" data-vbSelector="number_of_FactSpan_jobs"></i># of FactSpan jobs</a></li>
                        <li><a href="#" data-vb="hide" data-vbSelector="number_of_RelDir_jobs"></i># of RelDir jobs</a></li>
                        <li><a href="#" data-vb="hide" data-vbSelector="number_of_children"></i># of children</a></li>
                        <li><a href="#" data-vb="hide" data-vbSelector="parents"></i>parents</a></li>

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
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_cf_judgements" data-query-key="orderBy[cache.softwareAgent.cf]" data-toggle="tooltip" data-placement="top" title="Number of judgements the unit got on CrowdFlower"># CF judgements</th>
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_amt_judgements" data-query-key="orderBy[cache.softwareAgent.amt]" data-toggle="tooltip" data-placement="top" title="Number of judgements the unit got on AMT"># AMT judgements</th>
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_RelEx_jobs" data-query-key="orderBy[cache.jobs.types.RelEx.count]" data-toggle="tooltip" data-placement="top" title="Number of Relation Extraction jobs in which the unit was used"># of RelEx jobs</th>
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_FactSpan_jobs" data-query-key="orderBy[cache.jobs.types.FactSpan.count]" data-toggle="tooltip" data-placement="top" title="Number of Factor Span jobs in which the unit was used"># of FactSpan jobs</th>
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_RelDir_jobs" data-query-key="orderBy[cache.jobs.types.RelDir.count]" data-toggle="tooltip" data-placement="top" title="Number of Relation Direction jobs in which the unit was used"># of RelDir jobs</th>
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_children" data-query-key="orderBy[cache.children.count]" data-toggle="tooltip" data-placement="top" title="The number of units generated from this unit"># of children</th>
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="parents"  data-toggle="tooltip" data-placement="top" title="The units from which this media unit was created">parents</th>


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
                    <td data-vbIdentifier="number_of_cf_judgements">
                        <input class="input-sm form-control" type='text' data-query-key="match[cache.softwareAgent.cf]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
                        <input class="input-sm form-control" type='text' data-query-key="match[cache.softwareAgent.cf]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
                    </td>
                    <td data-vbIdentifier="number_of_amt_judgements">
                        <input class="input-sm form-control" type='text' data-query-key="match[cache.softwareAgent.amt]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
                        <input class="input-sm form-control" type='text' data-query-key="match[cache.softwareAgent.amt]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
                    </td>

                    <td data-vbIdentifier="number_of_RelEx_jobs">
                        <input class="input-sm form-control" type='text' data-query-key="match[cache.jobs.types.RelEx.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
                        <input class="input-sm form-control" type='text' data-query-key="match[cache.jobs.types.RelEx.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
                    </td>
                    <td data-vbIdentifier="number_of_FactSpan_jobs">
                        <input class="input-sm form-control" type='text' data-query-key="match[cache.jobs.types.FactSpan.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
                        <input class="input-sm form-control" type='text' data-query-key="match[cache.jobs.types.FactSpan.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
                    </td>
                    <td data-vbIdentifier="number_of_RelDir_jobs">
                        <input class="input-sm form-control" type='text' data-query-key="match[cache.jobs.types.RelDir.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
                        <input class="input-sm form-control" type='text' data-query-key="match[cache.jobs.types.RelDir.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
                    </td>
                    <td data-vbIdentifier="number_of_children">
                        <input class="input-sm form-control" type='text' data-query-key="match[cache.children.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
                        <input class="input-sm form-control" type='text' data-query-key="match[cache.children.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
                    </td>

                    <td data-vbIdentifier="parents">
                        <input class="input-sm form-control" type='text' data-query-key="match[parents][]" />
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
                        <td data-vbIdentifier="number_of_cf_judgements">@{{ this.cache.softwareAgent.cf }}</td>
                        <td data-vbIdentifier="number_of_amt_judgements">@{{ this.cache.softwareAgent.amt }}</td>
                        <td data-vbIdentifier="number_of_RelEx_jobs">@{{ this.cache.jobs.types.RelEx.count }}</td>
                        <td data-vbIdentifier="number_of_FactSpan_jobs">@{{ this.cache.jobs.types.FactSpan.count }}</td>
                        <td data-vbIdentifier="number_of_RelDir_jobs">@{{ this.cache.jobs.types.RelDir.count }}</td>
                        <td data-vbIdentifier="number_of_children">@{{ this.cache.children.count }}</td>
                        <td data-vbIdentifier="parents">@{{ this.parents }}</td>
			        </tr>
			        @{{/each}}
				</script>
	        </tbody>
	    </table>
    </div>											
</div>