<header class="site-header">
    <div class="container-fluid">

        <a href="{{ route('dashboard') }}" class="site-logo">
            <img class="hidden-md-down" src="/img/logo.png" alt="">
            <img class="hidden-lg-up" src="/img/logo-mobile.png" alt="">
        </a>

        <button class="hamburger hamburger--htla">
            <span>toggle menu</span>
        </button>

        <div class="site-header-content">
            <div class="site-header-content-in">
                <div class="site-header-shown">
                    <div class="dropdown user-menu">
                        <button class="dropdown-toggle" id="dd-user-menu" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="/img/avatar-64.png" alt=""> {!! auth()->user()->name !!}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-user-menu">
                            <a class="dropdown-item" href="#"><span class="font-icon glyphicon glyphicon-user"></span>Mein Profil</a>
                            <div class="dropdown-divider"></div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                            <a class="dropdown-item" href="{!! route('logout') !!}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span class="font-icon glyphicon glyphicon-log-out"></span>Ausloggen</a>
                        </div>
                    </div>
                </div><!--.site-header-shown-->

            </div><!--site-header-content-in-->
        </div><!--.site-header-content-->
    </div><!--.container-fluid-->
</header><!--.site-header-->