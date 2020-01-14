<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"> {$aUser.User_Username} <i class="fas fa-user"></i></a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="/{$aArura.dir}/profile">Profiel</a>
                <div class="dropdown-divider"></div>
                <button class="dropdown-item" href="#" onclick="LogOutUser()">Uitloggen</button>
            </div>
        </li>
            {if $sPageSideBar != NULL}
                <li class="nav-item">
                    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#">
                        <i class="fas fa-ellipsis-v"></i>
                    </a>
                </li>
            {/if}
    </ul>
</nav>