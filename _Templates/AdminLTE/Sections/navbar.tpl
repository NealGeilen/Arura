<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item">
            <h2 class="m-0 text-dark">{$title}</h2>
        </li>
    </ul>

    {if $smarty.now|date_format:"%Y-%m-%d" < $aWebsite.Launchdate}
        <div class="ml-2 d-block w-25">
            <div class="alert alert-info">
                <span><b>{$aWebsite.Launchdate|date_format:"%d-%m-%Y"}</b></span>
                <span>Website Launch</span>
            </div>
        </div>
    {/if}

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"> {$aUser.User_Username} <i class="fas fa-user"></i></a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="/{$aArura.dir}/profile">Profiel</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/{$aArura.dir}/logout" >Uitloggen</a>
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