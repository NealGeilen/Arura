{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/winkel/evenementen">Evenementen</a></li>
    <li class="breadcrumb-item active">{$Event->getName()}</li>
{/block}

{block content}
    {assign var="tabsType" value="tabs"}
    {include file="./tabs.tpl"}
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
                                {$eventForm->getControl("Event_IsActive")} <small>Inschrijving mogelijk.</small>
                            </div>
                            <div class="col-12">
                                {$eventForm->getControl("Event_IsVisible")} <small>Evenement is te zien in overzicht op website</small>
                            </div>
                            <div class="col-12">
                                {$eventForm->getControl("Event_IsPublic")} <small>Evenement url bereikbaar.</small>
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
                    {$Event->getTicketGrud()}
                </div>
            </div>
        </div>
            <div class="tab-pane fade" id="delete-event-tap" role="tabpanel" aria-labelledby="home-tab">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Verwijderen</h2>
                    </div>
                    <div class="card-body">
                        {if $Event->hasEventRegistrations()}
                            <div class="callout callout-warning">
                                <p>Een evenement kan niet verwijdert worden wanneer er inschrijvingen zijn.</p>
                            </div>
                        {else}
                            <form id="delete-event" method="post">
                                <input type="hidden" name="Event_Id" value="{$Event->getId()}" required>
                                <input type="hidden" name="type" value="delete-event" required>
                                <b>Verwijder {$Event->getName()}</b>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        {/if}
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Annuleren <span class="badge badge-beta">Beta</span></h2>
                    </div>
                    <div class="card-body">
                        {if $Event->isCanceled()}
                            <div class="callout callout-success">
                                <p><b>Evenement geannuleerd</b></p>
                                <p>{$Event->getCancelReason()}</p>
                            </div>
                        {else}
                            <div class="callout callout-info">
                                <p>Evenement wordt geannuleerd daarbij blijft de pagina toegankelijk voor iedereen. Echter wordt de inschrijving stop gezet en allen reeds ingeschreven op de hoogste gesteld over de mail van de annulering.</p>
                                <p><b>Geschreven tekst wordt publiekelijk vermeld.</b></p>
                            </div>
                            {$CancelForm->StartForm()}
                            {$CancelForm->getControl("Event_CancelReason", "richtext")}
                            {$CancelForm->getControl("submit")}
                            {$CancelForm->EndForm()}
                        {/if}
                    </div>
                </div>
            </div>
        {/if}
    </div>
{/block}