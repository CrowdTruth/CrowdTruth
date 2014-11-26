<div class="navbar-form navbar-right">
    @yield('dynamicButton')
    @if(Auth::check())

	<div class='btn-group'>
		<label class="btn btn-default" id='file_upload' for="my-file-selector" data-toggle="tooltip" data-placement="bottom" data-original-title="Upload CSV file with new data or existing results">
			<i class="fa fa-upload fa-fw"></i>
			<input type="file" id="my-file-selector" name="files[]" class="btn uploadInput hidden" multiple />
			Upload Data
		</label>
		<div class="btn-group vbColumns">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			<span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li role="presentation" class="dropdown-header"> Online Sources</li>
				<li><a href="#"><i class="fa fa-video-camera fa-fw"></i> Netherlands Institute for Sound and Vision Videos</a></li>
				<li><a href="#"><i class="fa fa-image fa-fw"></i> Rijksmuseum ImageGetter</a></li>
			</ul>
		</div>
	</div>
	
    <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i>{{ Auth::user()->firstname }}
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li><a href="#"><i class="fa fa-bar-chart fa-fw"></i> My Activity</a></li>
            <li><a href="{{ URL::to('/user/logout') }}"><i class="fa fa-sign-out fa-fw"></i> Log out</a></li>
        </ul> 
    </div>
    @else
       {{ link_to('user/login', "Log in", array("class" => "btn btn-primary")) }}
    @endif
</div>

@section('end_javascript')

<script>
$('document').ready(function(){

	$('body').tooltip({
		selector: '[data-toggle=tooltip]',
		container: 'body',
		html: true
	});

});
</script>

@stop