<div class="tab-pane" id="fullvideo_tab">	
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
					<li><a href="#" data-vb="show" data-vbSelector="video_content"></i>Video</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="video_name"></i>Name</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="video_identifier"></i>Identifier</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="video_source"></i>Source</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="video_title"></i>Title</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="video_subject"></i>Subject</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="video_description"></i>Description</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="video_abstract"></i>Abstract</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="video_date"></i>CreationDate</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="video_duration"></i>Duration</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="video_thumbnail"></i>Thumbnail</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="video_language"></i>Language</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="video_spacial"></i>Location</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="number_of_video_keyframes"></i># Keyframes</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="number_of_video_segments"></i># Segments</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="number_of_batches"></i>Used In # of Batches</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="number_of_jobs"></i>Used In # of Jobs</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="created_at"></i>Created At</a></li>
				</ul>
			</div>
			<select name="search_limit" class="selectpicker pull-right"><option value="10">10 Records per page</option><option value="25">25 Records per page</option><option value="50">50 Records per page</option><option value="100">100 Records per page</option><option value="1000">1000 Records per page</option></select></div></div> <div class='row'> <div class='col-xs-12'> <div class="btn-group pull-left searchStats">	</div> <div class='cw_pagination text-right'> </div> </div> </div> 
   <table class="table table-striped">
       	<thead data-query-key="match[documentType]" data-query-value="fullvideo">
	        <tr>
	            <th data-vbIdentifier="checkbox">Checkbox</th>
	            <th class="sorting" data-vbIdentifier="video_content" data-query-key="orderBy[content.identifier]">Video</th>
	            <th class="sorting" data-vbIdentifier="video_name" data-query-key="orderBy[content.videoName]">Name</th>
	            <th class="sorting" data-vbIdentifier="video_identifier" data-query-key="orderBy[content.identifier]">Identifier</th>
	            <th class="sorting" data-vbIdentifier="video_source" data-query-key="orderBy[source]">Source</th>
		    <th class="sorting" data-vbIdentifier="video_title" data-query-key="orderBy[content.metadata.title.nl]">Title</th>
	            <th class="sorting" data-vbIdentifier="video_subject" data-query-key="orderBy[content.metadata.subject.nl]">Subject</th>
		    <th class="sorting" data-vbIdentifier="video_description" data-query-key="orderBy[content.metadata.description]">Description</th>
		    <th class="sorting" data-vbIdentifier="video_abstract" data-query-key="orderBy[content.metadata.abstract]">Abstract</th>
		    <th class="sorting" data-vbIdentifier="video_date" data-query-key="orderBy[content.metadata.date]">CreationDate</th>
		    <th class="sorting" data-vbIdentifier="video_duration" data-query-key="orderBy[content.metadata.extent]">Duration</th>
		    <th class="sorting" data-vbIdentifier="video_language" data-query-key="orderBy[content.metadata.language]">Language</th>
		    <th class="sorting" data-vbIdentifier="video_spatial" data-query-key="orderBy[content.metadata.spatial.nl]">Location</th>
		    <th class="sorting" data-vbIdentifier="number_of_video_keyframes" data-query-key="orderBy[keyframes.count]"># Keyframes</th>
		    <th class="sorting" data-vbIdentifier="number_of_video_segments" data-query-key="orderBy[segments.count]"># Segments</th>
		    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_batches" data-query-key="orderBy[batches.count]">Used In # of Batches</th>
		    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_jobs" data-query-key="orderBy[jobs.count]">Used In # of Jobs</th>     
		    <th class="sorting whiteSpaceNoWrap" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]" style="min-width:220px; width:auto;">Created At</th>
	        </tr>
		<tr class="inputFilters">
			<td data-vbIdentifier="checkbox">
				<input type="checkbox" class="checkAll" />
			</td>
			<td data-vbIdentifier="video_content">
				<input type='text' data-query-key="match[content.identifier]" data-query-operator="like" />
			</td>
			<td data-vbIdentifier="video_name">
				<input type='text' data-query-key="match[content.videoName]" data-query-operator="like" />
			</td>
			<td data-vbIdentifier="video_identifier">
				<input type='text' data-query-key="match[content.identifier]" data-query-operator="like" />
			</td>
			<td data-vbIdentifier="video_source">
				<input type='text' data-query-key="match[source]" data-query-operator="like" />
			</td>
			<td data-vbIdentifier="video_title">
				<input type='text' data-query-key="match[content.metadata.title.nl]" data-query-operator="like" />	
			</td>
			<td data-vbIdentifier="video_subject">
				<input type='text' data-query-key="match[content.metadata.subject.nl]" data-query-operator="like" />
			</td>
			<td data-vbIdentifier="video_description">
				<input type='text' data-query-key="match[content.metadata.description.nl]" data-query-operator="like" />
			</td>
			<td data-vbIdentifier="video_abstract">
				<input type='text' data-query-key="match[content.metadata.abstract.nl]" data-query-operator="like" />
			</td>
			<td data-vbIdentifier="video_date">
				<div class="input-daterange" id="datepicker" data-date-start-view="2" data-date-format="yyyy" data-date-autoclose="true" data-date-min-view-mode="2">
					<input type="text" class="input-sm form-control" name="start" data-query-key="match[content.metadata.date]" data-query-operator=">=" style="width:49% !important; float:left;" placeholder="Start Date" />
					<input type="text" class="input-sm form-control" name="end" data-query-key="match[content.metadata.date]" data-query-operator="=<" style="width:49% !important; float:right;" placeholder="End Date" />
				</div>
			</td>
			<td data-vbIdentifier="video_duration">
				<input type='text' data-query-key="match[content.metadata.extent]" data-query-operator="like" />
			</td>
			<td data-vbIdentifier="video_language">
				<input type='text' data-query-key="match[content.metadata.language]" data-query-operator="like" />
			</td>
			<td data-vbIdentifier="video_spatial">
				<input type='text' data-query-key="match[content.metadata.spatial.nl]" data-query-operator="like" />
			</td>
			<td data-vbIdentifier="number_of_video_keyframes">
				<input type='text' data-query-key="match[keyframes.count]" data-query-operator=">" style="width:49%; float:left;" placeholder="gt" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input type='text' data-query-key="match[keyframes]" data-query-operator="<" style="width:49%; float:right;" placeholder="lt" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="number_of_video_segments">
				<input type='text' data-query-key="match[segments.count]" data-query-operator=">" style="width:49%; float:left;" placeholder="gt" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input type='text' data-query-key="match[segments.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="lt" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="number_of_batches">
				<input class="input-sm form-control" type='text' data-query-key="match[batches.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input class="input-sm form-control" type='text' data-query-key="match[batches.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="number_of_jobs">
				<input class="input-sm form-control" type='text' data-query-key="match[jobs.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input class="input-sm form-control" type='text' data-query-key="match[jobs.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
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
		        <tr class="text-center">
		            <td data-vbIdentifier="checkbox">Checkbox</td>
		            <td data-vbIdentifier="video_content">
				<video width="240" height="160" controls preload="none" poster="@{{ this.content.metadata.medium.thumbnail }}">
					
  					<source src="@{{ this.content.storage_url }}" type="video/mp4" >
					<source src="@{{ this.content.storage_url }}" type="video/ogg" >
						Your browser does not support the video tag.
					</source>
				</video>
			    </td>
		            <td data-vbIdentifier="video_name" ><a href="@{{ this.content.metadata.attributionURL }}" target="_blank" data-toggle="tooltip" data-placement="top" title="Click on the link to watch the video"> @{{ this.content.videoName }}</a></td>
		            <td data-vbIdentifier="video_identifier">@{{ this.content.identifier }}</td>
		            <td data-vbIdentifier="video_source">@{{ this.source }}</td>
		            <td data-vbIdentifier="video_title">@{{ this.content.metadata.title.nl }}</td>
			    <td data-vbIdentifier="video_subject">@{{ this.content.metadata.subject.nl }}</td>
		            <td data-vbIdentifier="video_description" >@{{ this.content.metadata.description.nl }}</td>
			    <td data-vbIdentifier="video_abstract" >@{{ this.content.metadata.abstract.nl }}</td>
			    <td data-vbIdentifier="video_date" >@{{ this.content.metadata.date }}</td>
      			    <td data-vbIdentifier="video_duration"> @{{ this.content.metadata.extent }}	</td>
			    <td data-vbIdentifier="video_language">@{{ this.content.metadata.language }}</td>
			    <td data-vbIdentifier="video_spatial" >@{{ this.content.metadata.spatial.nl }}</td>
			    <td data-vbIdentifier="number_of_video_keyframes" id="keyframe_@{{ @index }}">
				<a data-toggle="modal" data-target="#modal_@{{ @index }}" data-toggle="tooltip" data-placement="top" title="Click to see the keyframes">
					@{{ this.keyframes.count }}
				</a>

				<!-- Modal -->
				<div class="modal fade" id="modal_@{{@index}}" data-entityid="@{{ this._id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				  <div class="modal-dialog">
				    <div class="modal-content">
				      <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Keyframes of the Video</h4>
				      </div>
				      <div class="modal-body" >
					
				      </div>
				    </div>
				  </div>
				</div>
			    </td>
			    <td data-vbIdentifier="number_of_video_segments" id="segment_@{{ @index }}">
			    <a data-toggle="modal" data-target="#modalsegm_@{{ @index }}" data-toggle="tooltip" data-placement="top" title="Click to see the video segments">
					@{{ this.segments.count }}
				</a>

				<!-- Modal -->
				<div class="modal fade" id="modalsegm_@{{@index}}" data-entityid="@{{ this._id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				  <div class="modal-dialog">
				    <div class="modal-content">
				      <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Segments of the Video</h4>
				      </div>
				      <div class="modal-body" >
					
				      </div>
				    </div>
				  </div>
				</div>
			    </td>
			    <td data-vbIdentifier="number_of_batches">@{{ this.batches.count }}</td>
			    <td data-vbIdentifier="number_of_jobs">@{{ this.jobs.count }}</td>
			    <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>
			</tr>
		        @{{/each}}
			</script>
        </tbody>
    </table>
	<div class='hidden modalKeyFrameTemplate'>
		<table>
		<script class='template' type="text/x-handlebars-template">
		@{{#each documents}}
			<tr>
			<td><img src="@{{ this.content.storage_url }}" width="240" height="160" rel="tooltip" title="Timestamp: @{{ this.content.timestamp }} &#xA; Source name: @{{ this.content.storage_url }} "/></td>
			<td><strong>Timestamp</strong>: @{{ this.content.timestamp }}</td>
			</tr>
		@{{/each}}
		</script>
		</table>
	</div>	

	<div class='hidden modalVideoSegmentTemplate'>
		<table>
		<script class='template' type="text/x-handlebars-template">
		@{{#each documents}}
			<tr>
			<td><video width="240" height="160" controls preload="none">
				<source src="@{{ this.content.storage_url }}" type="video/mp4" >
				<source src="@{{ this.content.storage_url }}" type="video/ogg" >
					Your browser does not support the video tag.
				</source>
			</video></td>
			<td align="left"><strong>Duration</strong>: @{{ this.content.duration }} <br />
<strong>Start Time</strong>: @{{ this.content.start_time }} <br />
<strong>End Time</strong>: @{{ this.content.end_time }} <br />
			</td>
			</tr>
		@{{/each}}
		</script>
		</table>
	</div>										
</div>
