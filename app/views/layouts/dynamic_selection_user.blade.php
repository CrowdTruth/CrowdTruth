<div class="navbar-form navbar-right">
    @yield('dynamicButton')
    @if(Auth::check())
    <div class="btn-group">
		<a href='{{ URL::to('media/upload') }}' class="{{ (Request::is('media/upload') ? 'active' : '') }} btn btn-default"><i class="fa fa-upload fa-fw"></i> Upload Media</a>
    </div>
    <div class="btn-group">
        <button type="button" class="btn userButton dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i> {{ Auth::user()->firstname }}
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li><a href="{{ URL::to('/user/activity') }}"><i class="fa fa-bar-chart fa-fw"></i> My activity</a></li>
            <li><a href="{{ URL::to('/user/logout') }}"><i class="fa fa-sign-out fa-fw"></i> Log out</a></li>
        </ul> 
    </div>
    @else
       {{ link_to('user/login', "Log in", array("class" => "btn btn-primary")) }}
    @endif
</div>