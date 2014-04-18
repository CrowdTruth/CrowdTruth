<div data-pagination="annotation in annotations" data-num-pages="numPages()" 
      data-current-page="currentPage" data-max-size="maxSize"  
      data-boundary-links="true"></div>
<div ng-repeat="unit in units" class="items">
	<div class="row">
		<div class="col-md-8">
			<a ng-click="gotoUnit(unit._id)"><h4>@{{unit.title}}</h4></a>
			<label class="ann-label">@{{unit.domain}} @{{unit.format}}</label><br>
			<label class="ann-label">@{{unit.documentType}}</label><br>
			<label class="ann-label">Created by </label>@{{unit.user_id}} <label> with </label> @{{unit.activity_id}} <label> on </label> @{{unit.created_at}}
		</div>
	</div>
	<div>
		<pre style="height: 300px;">@{{unit.content | json}}</pre>
	</div>
</div>