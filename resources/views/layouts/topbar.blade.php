<nav class="navbar navbar-expand navbar-theme">
    <a class="sidebar-toggle d-flex mr-2">
        <i class="hamburger align-self-center"></i>
    </a>

{{--    <form class="form-inline d-none d-sm-inline-block" action="javascript: void(0);" method="get">--}}
{{--        <input class="form-control form-control-lite" type="text" placeholder="Search products...">--}}
{{--    </form>--}}

    <div class="navbar-collapse collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown active">
                <a class="nav-link dropdown-toggle position-relative" href="#" id="userDropdown" data-toggle="dropdown">
                    <i class="align-middle fas fa-cog"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="{{ route('user.logout') }}"><i class="align-middle mr-1 fas fa-fw fa-arrow-alt-circle-right"></i> Log out</a>
                </div>
            </li>
        </ul>
    </div>

</nav>
