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
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#sessions" role="tab" aria-controls="contact" aria-selected="false">Sessies</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="gegevens" role="tabpanel" aria-labelledby="home-tab">
            <div class="card card-primary">
                <div class="card-body">
                    {$editForm}
                </div>
            </div>
            <div class="card card-secondary bg-secondary">
                <div class="card-body">
                    {if $apiForm->isSubmitted()}
                        <div class="alert alert-warning mb-3">
                            Nieuwe token: "{$User->getApiToken()}" <br/> Eenmalig beschikbaar!
                        </div>
                    {/if}
                    {$apiForm}
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
        <div class="tab-pane fade" id="sessions" role="tabpanel" aria-labelledby="contact-tab">
            <div class="card card-secondary">
                <div class="card-body table-responsive">
                    <table class="table Arura-Table">
                        <thead>
                        <tr>
                            <th>
                                Session id
                            </th>
                            <th>
                                Ip address
                            </th>
                            <th>
                                Tijd laatste actie
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $aSessions as $aSession}
                            <tr>
                                <td>{$aSession.Session_Id}</td>
                                <td>{$aSession.Session_Ip}</td>
                                <td>{$aSession.Session_Last_Active|date_format:"%H:%M %d-%m-%Y"}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
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
                                <h3 class="timeline-header"><b>{$Log.Entity.Name}</b><br/> {$Log.Status.Name}, {$Log.Logger_Name}</h3>
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