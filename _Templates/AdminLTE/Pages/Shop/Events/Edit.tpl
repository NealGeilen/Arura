{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/winkel/evenementen">Evenementen</a></li>
    <li class="breadcrumb-item active">{$aEvent.Event_Name}</li>
{/block}

{block content}
    {$aPermissions|var_dump}
    <ul class="nav nav-tabs" role="tablist">
        {if $aPermissions.SHOP_EVENTS_MANAGEMENT}
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#gegevens" role="tab">Gegevens</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tickets-tabe" role="tab">Tickets</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#delete-event-tap" role="tab">Verwijderen</a>
            </li>
        {/if}
        {if $aPermissions.SHOP_EVENTS_REGISTRATION}
        <li class="nav-item">
            <a class="nav-link" href="?t=registrations" role="tab">Aanmeldingen</a>
        </li>
        {/if}
        {if $aPermissions.SHOP_EVENTS_VALIDATION}
        <li class="nav-item">
            <a class="nav-link" href="?t=validation">Valideren</a>
        </li>
        {/if}
    </ul>
    <div class="tab-content">
        {if $aPermissions.SHOP_EVENTS_MANAGEMENT}
            <div class="tab-pane fade show active" id="gegevens" role="tabpanel" aria-labelledby="home-tab">
                <div class="card">
                    <div class="card-body">
                        {$eventForm->StartForm()}
                        <div class="row">
                            <div class="col-md-6">
                                {$eventForm->getControl("Event_Name")}
                            </div>
                            <div class="col-md-6">
                                {$eventForm->getControl("Event_Slug")}
                            </div>
                            <div class="col-12">
                                {$eventForm->getControl("Event_Location")}
                            </div>
                            <div class="col-md-6">
                                {$eventForm->getControl("Event_Start_Timestamp")}
                            </div>
                            <div class="col-md-6">
                                {$eventForm->getControl("Event_End_Timestamp")}
                            </div>
                            <div class="col-12">
                                {$eventForm->getControl("Event_Description", "richtext")}
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-md-6">
                                {$eventForm->getControl("Event_Banner", "file-selector")}
                            </div>
                            <div class="col-md-6">
                                {$eventForm->getControl("Event_Registration_End_Timestamp")}
                            </div>
                            <div class="col-md-6">
                                {$eventForm->getControl("Event_Capacity")}
                            </div>
                            <div class="col-md-6">
                                {$eventForm->getControl("Event_Organizer_User_Id")}
                            </div>
                            <div class="col-12">
                                {$eventForm->getControl("Event_IsActive")}
                                {$eventForm->getControl("Event_IsVisible")}
                            </div>
                        </div>
                        {$eventForm->getControl("submit")}
                        {$eventForm->EndForm()}

                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tickets-tabe" role="tabpanel" aria-labelledby="home-tab">
            <div class="card" id="tickets">
                <div class="card-body table-responsive" id="tickets">
                    {$sTicketsCrud}
                </div>
            </div>
        </div>
            <div class="tab-pane fade" id="delete-event-tap" role="tabpanel" aria-labelledby="home-tab">
                <div class="card">
                    <div class="card-body">
                        {if $bHasEventTicketsSold}
                            <div class="alert alert-info">
                                Een evenement kan niet verwijdert worden wanneer er inschrijvingen zijn.
                            </div>
                        {else}
                            <form id="delete-event" method="post">
                                <input type="hidden" name="Event_Id" value="{$aEvent.Event_Id}" required>
                                <input type="hidden" name="type" value="delete-event" required>
                                Verwijder {$aEvent.Event_Name}
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        {/if}
                    </div>
                </div>
            </div>
        {/if}
    </div>
{/block}