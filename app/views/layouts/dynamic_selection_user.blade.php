<div class="navbar-form navbar-right">
    @yield('dynamicButton')
    @if(Auth::check())
    <div class="btn-group">
        <button type="button" class="btn userButton dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i>{{ Auth::user()->firstname }}
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li><a href="#"><i class="fa fa-folder-open fa-fw"></i>My activity</a></li>
            <li><a href="{{ URL::to('/user/logout') }}"><i class="fa fa-sign-out fa-fw"></i>Log out</a></li>
        </ul> 
    </div>
    @else
       {{ link_to('user/login', "Log in", array("class" => "btn btn-primary")) }}
    @endif
</div>