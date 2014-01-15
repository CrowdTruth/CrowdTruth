<!-- START process_nav -->   
						<ul class="nav nav-tabs" id="processtabs">
							<li{{ (Request::segment(2) == 'selectfile' ? ' class="active"' : '') }} title='selectfile'>{{ link_to('process/selectfile', "Select file") }}</li>
							<li{{ (Request::segment(2) == 'template' ? ' class="active"' : '') }} title='template'>{{ link_to('process/template', "Pick/build Template") }}</li>
							<li{{ (Request::segment(2) == 'details' ? ' class="active"' : '') }} title='details'>{{ link_to('process/details', "Job Details") }}</li>
							<li{{ (Request::segment(2) == 'platform' ? ' class="active"' : '') }} title='platform'>{{ link_to('process/platform', "Platform") }}</li>
							<li{{ (Request::segment(2) == 'submit' ? ' class="active"' : '') }} title='submit'>{{ link_to('process/submit', "Submit") }}</li>
							<li{{ (Request::segment(2) == 'amt' ? ' class="active"' : '') }} title='amt'>{{ link_to('process/amt', "AMT") }}</li>
						</ul>
<!-- END process_nav   -->   
@section('end_javascript')
<script>
$(document).ready(function(){
	$("#processtabs > li").click(function(event){
		if($(".crowdtask").prop("action").length > 0) {
			event.preventDefault();
	       $(".crowdtask").prop("action", "/process/form-part/" + $(this).prop('title')).submit();
		}
	});
});
<?php if(isset($templatePath)){ ?>
	$("select[name='template']" ).change(function(){
       $("#question").attr('src', "{{ $templatePath }}"+ $( "select[name='template'] option:selected").val()+'.html');
    });
<?php } ?>
</script>
@endsection