{extends "../../index.tpl"}
{block content}
    <div class="container">
        <div class="row">
            <div class="col-6 col-md-4">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>Hallo</h3>

                        <p>Welkom {$aUser.User_Username}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
            {if $smarty.now|date_format:"%Y-%m-%d" < $aWebsite.Launchdate}
                <div class="col-6 col-md-4">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{$aWebsite.Launchdate|date_format:"%d/%m/%Y"}</h3>

                            <p>Website Launch</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                    </div>
                </div>
            {/if}
            {if $iUserCount != null}
                <div class="col-6 col-md-4">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{$iUserCount}</h3>

                            <p>Active gebruikers</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            {/if}
            {if $iPageCount !== null}
                <div class="col-md-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{$iPageCount}</h3>

                            <p>Active Pagina's</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-globe-europe"></i>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>

{/block}