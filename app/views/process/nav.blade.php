
<!-- START process_nav -->   
						<ul class="nav nav-tabs" id="processtabs">
							<li{{ (Request::segment(2) == 'selectfile' ? ' class="active"' : '') }} title='selectfile'>{{ link_to('process/selectfile', "Select file") }}</li>
							<li{{ (Request::segment(2) == 'template' ? ' class="active"' : '') }} title='template'>{{ link_to('process/template', "Pick/build Template") }}</li>
							<li{{ (Request::segment(2) == 'details' ? ' class="active"' : '') }} title='details'>{{ link_to('process/details', "Job Details") }}</li>
							<li{{ (Request::segment(2) == 'platform' ? ' class="active"' : '') }} title='platform'>{{ link_to('process/platform', "Platform") }}</li>
							<li{{ (Request::segment(2) == 'submit' ? ' class="active"' : '') }} title='submit'>{{ link_to('process/submit', "Submit") }}</li>
							<li{{ (Request::segment(2) == 'amt' ? ' class="active"' : '') }} title='amt'>{{ link_to('process/amt', "AMT") }}</li>
							<a href='/process/clear-task' class="btn btn-danger pull-right">Reset form</a></li>

						</ul>


<!-- END process_nav   -->   
@section('end_javascript')
{{ javascript_include_tag('jstree/jstree.min.js') }}
{{ javascript_include_tag('jstree/libs/require.js') }}
<?= stylesheet_link_tag('jstree/style.min.css') ?>
<script>
$(document).ready(function(){
	$("#processtabs > li").click(function(event){
		if($(".crowdtask").prop("action").length > 0) {
			event.preventDefault();
	       $(".crowdtask").prop("action", "/process/form-part/" + $(this).prop('title')).submit();
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
		
});

@if(isset($templatePath))
	$("select[name='template']" ).change(function(){
       $("#question").attr('src', "{{ $templatePath }}"+ $( "select[name='template'] option:selected").val()+'.html');
    });
@endif


</script>
@endsection
