{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item active">Logs</li>
{/block}


{block content}
    {foreach $Logs as $Log}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <h4 class="m-0">{$Log.channel} <span class="badge {if $Log.level >= 100} badge-secondary{/if}{if $Log.level >= 200} badge-info{/if}{if $Log.level >= 300} badge-warning{/if}{if $Log.level >= 500} badge-danger{/if}">{$Levels[{$Log.level}]}</span></h4>
                    </div>
                    <div class="col-5">
                        <span class="text-truncate w-100 d-block">{$Log.message}</span>
                    </div>
                    <div class="col-4">
                        <span class="float-right">
                            {$Log.time|date_format:"%H:%M %d-%m-%y"}
                            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#Log-{$Log.id}" aria-expanded="false" aria-controls="collapseExample">
                                <i class="fa fa-plus"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="collapse" id="Log-{$Log.id}">
                    <div class="p-3 text-red">
                        {$Log.message}
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <b>Url</b> {$Log.Requested_Url}
                                </li>
                                <li class="list-group-item">
                                    <b>Session id</b> {$Log.Session_Id}
                                </li>
                                <li class="list-group-item">
                                    <b>Ip</b> {$Log.Request_Ip}
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