<div data-pagination="annotation in annotations" data-num-pages="numPages()" 
      data-current-page="currentPage" data-max-size="maxSize"  
      data-boundary-links="true"></div>
<div ng-repeat="job in jobs" class="items">
	<div class="row">
		<div class="col-md-8">
			<h4>@{{job.format}} @{{job.domain}} @{{job.type}}</h4>
			<label class="ann-label">Batch: </label>@{{job.batch_id}}<br>
			<label class="ann-label">Created by </label>@{{job.activity_id}} <label> on </label> @{{job.created_at}}
		</div>
		<div class="col-md-4 anntime">
			<label class="ann-label">Status:  </label>@{{job.status}} <span ng-show="job.status =='running'"> @{{job.completion * 100 | number: 2}} %</span><br>
			<label class="ann-label">Annotations: </label>@{{job.annotationsCount}}<br>
			<label class="ann-label">Units: </label>@{{job.unitsCount}}<br>
		</div>
	</div>
</div>