
<!-- START process_nav -->   

						<ul class="nav nav-tabs" id="processtabs">
							<li{{ (Request::segment(2) == 'selectfile' ? ' class="active"' : '') }} title='selectfile'>{{ link_to('process/selectfile', "Select file") }}</li>
							<li{{ (Request::segment(2) == 'template' ? ' class="active"' : '') }} title='template'>{{ link_to('process/template', "Pick/build Template") }}</li>
							<li{{ (Request::segment(2) == 'details' ? ' class="active"' : '') }} title='details'>{{ link_to('process/details', "Job Details") }}</li>
							<li{{ (Request::segment(2) == 'platform' ? ' class="active"' : '') }} title='platform'>{{ link_to('process/platform', "Platform") }}</li>
							<li{{ (Request::segment(2) == 'submit' ? ' class="active"' : '') }} title='submit'>{{ link_to('process/submit', "Submit") }}</li>
							<a href='/process/clear-task' class="btn btn-danger pull-right">Reset form</a></li>

						</ul>


<!-- END process_nav   -->   
<!-- TODO: put this in a better place. -->
@section('end_javascript')
<script src="/custom_assets/bootstrap-select.min.js"></script>
<link rel="stylesheet" type="text/css" href="/custom_assets/bootstrap-select.min.css">

<script>
$('.selectpicker').selectpicker();

$('#deselectcountries').click(function(){
	$('#countries').selectpicker('deselectAll');
})

$('#englishcountries').click(function(){
	$('#countries').selectpicker('val', {{ Config::get('config.englishcountries') }}); //'IE', 'NZ', 'JA'
})

$('#customcountries').click(function(){
	$('#countries').selectpicker('val', {{ Config::get('config.customcountries')['countries'] }}); //'AW', 
})



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
        var judgmentsPerUnit = $('#judgmentsPerUnit').val();
        var unitsPerTask = $('#unitsPerTask').val()
        //var sentences = $
		var cost = (reward*judgmentsPerUnit)/unitsPerTask;
		var result = "<strong> $ " + cost.toFixed(2) + "</strong>";
        var el = document.getElementById('totalCost')
        if(el) el.innerHTML=result;
    } 

</script>
@endsection
