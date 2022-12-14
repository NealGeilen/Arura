{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item active">Logs</li>
{/block}


{block content}
    {foreach $Logs as $Log}
        <div class="card">
            <div class="card-body position-relative">
                <div class="position-absolute" style="right: 1rem; top: 1rem; z-index: 100">
                    {$Log.time|date_format:"%H:%M %d-%m-%y"}
                            <button class="btn btn-primary" data-toggle="collapse" data-target="#Log-{$Log.id}">
                                <i class="fa fa-plus"></i>
                            </button>
                </div>
                <div class="row" style="z-index: 50">
                    <div class="col-md-3 col-12">
                        <h4 class="m-0">{$Log.channel} <span class="badge {if $Log.level >= 100} bg-secondary{/if}{if $Log.level >= 200} bg-info{/if}{if $Log.level >= 300} bg-warning{/if}{if $Log.level >= 500} bg-danger{/if}">{$Levels[{$Log.level}]}</span></h4>
                    </div>
                    <div class="col-md-8 col-12">
                        <span class="text-truncate w-75 d-block text-danger">{$Log.message|escape:'html'}</span>
                    </div>
                </div>
                <div class="collapse" id="Log-{$Log.id}">
                    <div class="p-3 text-danger">
                        {$Log.message|escape:'html'}
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <b>Url</b> {$Log.Requested_Url|escape:'html'}
                                </li>
                                <li class="list-group-item">
                                    <b>Session id</b> {$Log.Session_Id|escape:'html'}
                                </li>
                                <li class="list-group-item">
                                    <b>Ip</b> {$Log.Request_Ip|escape:'html'}
                                </li>
                            </ul>
                        </div>
                            {if $Log.User_Id}
                                <div class="col-md-6">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            {$Log.User_Firstname} {$Log.User_Lastname}
                                        </li>
                                        <li class="list-group-item">
                                            {$Log.User_Email}
                                        </li>
                                    </ul>
                                </div>
                            {/if}
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
{/block}