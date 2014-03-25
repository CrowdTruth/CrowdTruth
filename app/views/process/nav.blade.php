
<!-- START process_nav -->   

						<ul class="nav nav-tabs" id="processtabs">
							<li{{ (Request::segment(2) == 'batch' ? ' class="active"' : '') }} title='batch'>{{ link_to('process/selectbatch', "1. Batch") }}</li>
							<li{{ (Request::segment(2) == 'template' ? ' class="active"' : '') }} title='template'>{{ link_to('process/template', "2. Template") }}</li>
							<li{{ (Request::segment(2) == 'platform' ? ' class="active"' : '') }} title='platform'>{{ link_to('process/platform', "3. Platform") }}</li>
							<li{{ (Request::segment(2) == 'details' ? ' class="active"' : '') }} title='details'>{{ link_to('process/details', "4. Job Details") }}</li>
							<?php $count = 0; ?>
							@if(isset($jobconf) && isset($jobconf['platform']))
								@foreach ($jobconf['platform'] as $p)
								<?php $count++; $link = "process/$p"; $ptoupper = strtoupper($p); ?>
							<li{{ (Request::segment(2) == $p ? ' class="active"' : '') }} title="{{$p}}">{{ link_to($link, "5.$count Platform: $ptoupper") }}</li>
								@endforeach
							@endif
							<li{{ (Request::segment(2) == 'submit' ? ' class="active"' : '') }} title='submit'>{{ link_to('process/submit', "5. Submit") }}</li>
							<a href='/process/clear-task' class="btn btn-danger pull-right">Reset form</a></li>

						</ul>


<!-- END process_nav   -->   
<!-- TODO: put this in a better place. -->
@section('end_javascript')
<script src="/custom_assets/bootstrap-select.min.js"></script>
<link rel="stylesheet" type="text/css" href="/custom_assets/bootstrap-select.min.css">

<script>
$(document).ready(function(){
	$("#processtabs > li").click(function(event){
		if($(".jobconf").prop("action").length > 0) {
			event.preventDefault();
	       $(".jobconf").prop("action", "/process/form-part/" + $(this).prop('title')).submit();
		}
	});



@if (isset($treejson))
	$('#jstree').jstree({ 'core' : {
	"theme" : {
      "variant" : "large",
      "icons" : "false"
    },	
    'data' : {{ $treejson }},

} }).on('changed.jstree', function (e, data) {
	var parent =  data.instance.get_node(data.selected).parent;
	var self = data.instance.get_node(data.selected).id;
	if(!(data.instance.is_parent(data.selected))){
		$("#question").attr('src', '/templates/' +parent + '/' + self + '.html');
		$("#template").val(parent + '/' + self);
	}
    //data.instance.get_node(data.selected).text
  });
@endif

});

$(window).load(function() {
  		$('#amt-button').change(function () {                
     		$('#amt-div').toggle(this.checked);
  			}).change(); //ensure visible state matches initially
				
  		$('#cf-button').change(function () {                
     		$('#cf-div').toggle(this.checked);
     		$('#cflabel').button('toggle');
  			}).change(); //ensure visible state matches initially
		calculate();
});

    function calculate(){
        var reward = $('#reward').val();
        var annotationsPerUnit = $('#annotationsPerUnit').val();
        var unitsPerTask = $('#unitsPerTask').val();
        var expirationInMinutes = $('#expirationInMinutes').val();
        var unitsCount = {{ $unitscount or 0}};
		var costPerUnit = (reward/unitsPerTask)*annotationsPerUnit;
		var rph = (reward/expirationInMinutes)*60;

        var el = document.getElementById('costPerUnit');
	    if(el) el.innerHTML= "$" + costPerUnit.toFixed(2);

	    var el0 = document.getElementById('minRewardPerHour');
	    if(el0) el0.innerHTML= "$" + rph.toFixed(2);
	    
	    if(unitsCount > 0) {
	    	var totalCost = (reward/unitsPerTask)*(unitsCount*annotationsPerUnit);
	        var el1 = document.getElementById('totalCost');
	        if(el1) el1.innerHTML= "<strong>$" + totalCost.toFixed(2)  + "</strong>";
    	}
    } 

</script>
@endsection


