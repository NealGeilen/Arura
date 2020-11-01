{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Activiteiten: {$aUser.User_Username}</li>
{/block}

{block content}
    <div class="timeline">
        {foreach $Logs as $Day => $LogsPerDay}
            <div class="time-label">
                <span class="bg-primary">{$Day}</span>
            </div>
            {foreach $LogsPerDay as $Log}
                <div>
                    <i class="{$Log.Entity.Icon} {$Log.Status.Color}"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {$Log.Logger_Time|date_format:"%H:%M:%S"}</span>
                        <h3 class="timeline-header"><b>{$Log.Entity.Name}</b><br/> {$Log.Status.Name}, {$Log.Logger_Name}</h3>
                    </div>
                </div>
            {/foreach}

        {/foreach}
        <div>
            <i class="fas fa-clock bg-gray"></i>
        </div>
    </div>
{/block}