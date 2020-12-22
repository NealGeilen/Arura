{extends "../../index.tpl"}
{block content}
    <script>
        var JSONUserActions = '{$JSONUserActions}';
        var JSONEventRegistrations = '{$JSONEventRegistrations}';
        var JSONPayments = '{$JSONPayments}';
    </script>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">Welkom {$aUser.User_Firstname} {$aUser.User_Lastname}</div>
                </div>
                <div class="card-body bg-primary">
                    <canvas class="TimeLine"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        {if !empty($Events)}
            <div class="col-12 col-md-6">
                <div class="card card-secondary bg-secondary">
                    <div class="card-header">
                        <h2 class="card-title">Evenementen</h2>
                        <div class="card-tools">
                            <button class="btn btn-secondary btn-sm" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
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
        {if !empty($Galleries)}
            <div class="col-12 col-md-6">
                <div class="card card-secondary bg-secondary">
                    <div class="card-header">
                        <h2 class="card-title">Albums</h2>
                        <div class="card-tools">
                            <button class="btn btn-secondary btn-sm" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
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
    </div>
{/block}