{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/arura/users">Gebruikers</a></li>
    <li class="breadcrumb-item active">{$User->getFirstname()}</li>
{/block}
{block content}
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#gegevens" role="tab" aria-controls="home" aria-selected="true">Gegevens</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#password" role="tab" aria-controls="profile" aria-selected="false">Wachtwoord</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#rolles" role="tab" aria-controls="contact" aria-selected="false">Rollen</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#logger" role="tab" aria-controls="contact" aria-selected="false">Acties</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="gegevens" role="tabpanel" aria-labelledby="home-tab">
            <div class="card card-primary">
                <div class="card-body">
                    {$editForm}
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="profile-tab">
            <div class="card card-secondary">
                <div class="card-body">
                    {$passwordForm}
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="rolles" role="tabpanel" aria-labelledby="contact-tab">
            <div class="card card-secondary">
                <div class="card-body">
                    {$roleForm}
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="logger" role="tabpanel" aria-labelledby="contact-tab">
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
                                <h3 class="timeline-header"><b>{$Log.Status.Name}</b> </h3>

                                <div class="timeline-body">
                                    {$Log.Entity.Name} {$Log.Logger_Name} {*<a class="btn btn-danger btn-sm float-md-right text-white"><i class="far fa-trash-alt"></i></a>*}
                                </div>
                            </div>
                        </div>
                    {/foreach}

                {/foreach}
                <div>
                    <i class="fas fa-clock bg-gray"></i>
                </div>
            </div>
        </div>
    </div>
{/block}