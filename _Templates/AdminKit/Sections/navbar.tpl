<nav class="navbar navbar-expand navbar-light navbar-bg">
    <a class="sidebar-toggle d-flex">
        <i class="hamburger align-self-center"></i>
    </a>

    <div class="navbar-collapse collapse">
        <ul class="navbar-nav navbar-align">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <span class="text-dark">{$aUser["User_Firstname"]} {$aUser["User_Lastname"]}</span>                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="/dashboard/profile">
                        Profiel
                    </a>
                    <a class="dropdown-item" href="/dashboard/logout">
                        Uitloggen
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>