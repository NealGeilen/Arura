{extends "../../index.tpl"}
{block content}
    <div class="row">
        <div class="col-12 col-md-4">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>Hallo</h3>

                    <p>Welkom {$aUser.User_Firstname} {$aUser.User_Lastname}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>
        {if $smarty.now|date_format:"%Y-%m-%d" < $aWebsite.Launchdate}
            <div class="col-12 col-md-4">
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
            <div class="col-12 col-md-4">
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
            <div class="col-md-4 col-12">
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
    <div class="row">
        {if $aPermissions.ANALYTICS}
            <div class="col-12 col-md-6">
                <div class="card card-secondary VisitorsDays">
                    <div class="card-header">
                        <h3 class="card-title">Bezoekers per dag</h3>
                        <div class="card-tools">
                            <button class="btn btn-secondary btn-sm" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <canvas></canvas>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                    <div class="overlay">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                </div>
            </div>
        {/if}
        {if $aPermissions.SHOP_EVENTS_MANAGEMENT}
            <div class="col-12 col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h2 class="card-title">Evenementen</h2>
                        <div class="card-tools">
                            <button class="btn btn-primary btn-sm" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        {foreach from=$Events key=$iKey item=Event}
                            <a href="/{$aArura.dir}/winkel/evenement/{$Event->getId()}">
                                <div class="info-box">

                                <span class="info-box-icon {if $iKey === 0}bg-primary{else}bg-secondary{/if}">
                                    {$Event->getAmountSignIns()}
                                </span>

                                    <div class="info-box-content">
                                    <span class="info-box-text text-dark">
                                        <b class="text-capitalize">{$Event->getName()}</b>
                                        <br/>
                                        <span class="badge badge-info">{$Event->getStatus()}</span>
                                        <br/>
                                        {$Event->getStart()->format("U")|date_format:"%H:%M %d-%m-%y"} t/m {$Event->getEnd()->format("U")|date_format:"%H:%M %d-%m-%y"}
                                    </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </a>

                        {/foreach}
                    </div>
                </div>
            </div>
        {/if}
        {if $aPermissions.GALLERY_MANGER}
            <div class="col-12 col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h2 class="card-title">Albums</h2>
                        <div class="card-tools">
                            <button class="btn btn-info btn-sm" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        {foreach from=$Galleries key=$iKey item=Gallery}
                            <a href="/{$aArura.dir}/gallery/{$Gallery->getId()}">
                                <div class="info-box">
                                <span class="info-box-icon {if $iKey === 0}bg-primary{else}bg-secondary{/if}">
                                    {$Gallery->getImageAmount()}
                                </span>

                                    <div class="info-box-content">
                                    <span class="info-box-text text-dark">
                                        <b class="text-capitalize">{$Gallery->getName()}</b>
                                        {if !$Gallery->isPublic()}
                                            <br/>
                                            <span class="badge badge-warning">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        {/if}
                                        <br/>
                                        Aangemaakt:
                                        <span class="badge badge-info">
                                            {$Gallery->getCreatedDate()->format("d-m-Y H:i")}
                                        </span>
{*                                        <br/>*}
{*                                        {$Event->getStart()->format("U")|date_format:"%H:%M %d-%m-%y"} t/m {$Event->getEnd()->format("U")|date_format:"%H:%M %d-%m-%y"}*}
                                    </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </a>

                        {/foreach}
                    </div>
                </div>
            </div>
        {/if}
        {if $aPermissions.SHOP_PAYMENTS}
            <div class="col-12 col-md-6">
                <div class="card card-secondary paymentsTimeLine">
                    <div class="card-header">
                        <h2 class="card-title">Betalingen van de afgelopen 7 dagen</h2>
                        <div class="card-tools">
                            <button class="btn btn-secondary btn-sm" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas></canvas>
                    </div>
                    <div class="overlay">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                </div>
            </div>
        {/if}

    </div>
{/block}