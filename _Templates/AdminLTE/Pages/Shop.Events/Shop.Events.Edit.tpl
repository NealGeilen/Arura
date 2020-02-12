<div class="card">
    <div class="card-header">
        <h2 class="card-title">Evenement eigenschappen</h2>
        <div class="card-tools">
            <div class="btn-group btn-group-sm">
                <a class="btn btn-primary" href="/{$aArura.dir}/winkel/evenementen/beheer"><i class="fas fa-long-arrow-alt-left"></i></a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="form-row form-sender" action="/{$aArura.dir}/api/shop/event.php?type=save-event" method="post">
            <input type="hidden" name="Event_Id" value="{$aEvent.Event_Id}" required>
            <div class="col-12">
                <h6>Basis</h6>
            </div>
            <div class="form-group col-md-4 col-6">
                <label>Naam</label>
                <input type="text" class="form-control" name="Event_Name" value="{$aEvent.Event_Name}" required>
            </div>
            <div class="form-group col-md-4 col-6">
                <label>Slug</label>
                <input type="text" class="form-control" name="Event_Slug" value="{$aEvent.Event_Slug}" required>
            </div>
            <div class="form-group col-md-4 col-6">
                <label>Locatie</label>
                <input type="text" class="form-control" name="Event_Location" value="{$aEvent.Event_Location}" required>
            </div>
            <div class="form-group col-12">
                <label>Omschrijving</label>
                <textarea class="richtext" name="Event_Description">{$aEvent.Event_Description}</textarea>
            </div>
            <div class="form-group col-md-6 col-6">
                <label>Start datum</label>
                <input type="datetime-local" class="form-control" name="Event_Start_Timestamp" value="{$aEvent.Event_Start_Timestamp|date_format:"%Y-%m-%dT%H:%M"}" required>
            </div>
            <div class="form-group col-md-6 col-6">
                <label>Eind datum</label>
                <input type="datetime-local" class="form-control" name="Event_End_Timestamp" value="{$aEvent.Event_End_Timestamp|date_format:"%Y-%m-%dT%H:%M"}" required>
            </div>
            <div class="col-12">
                <hr/>
                <h6>Details</h6>
            </div>
            <div class="form-group col-md-4 col-6">
                <label>Banner evenement</label>
                <input type="text" class="form-control file-selector" name="Event_Banner" value="{$aEvent.Event_Banner}" required>
            </div>
            <div class="form-group col-md-4 col-6">
                <label>Organizator</label>
                <select class="form-control" value="{$aEvent.Event_Organizer_User_Id}" name="Event_Organizer_User_Id" required>
                    {foreach from=$aUsers item=user}
                    <option value="{$user.User_Id}">{$user.User_Username} | {$user.User_Email}</option>
                    {/foreach}
                </select>
            </div>

            {if !$bTickets}
            <div class="form-group col-md-4 col-6">
                <label>Capaciteit</label>
                <input type="number" class="form-control" name="Event_Capacity" value="{$aEvent.Event_Capacity}" min="0" required>
            </div>
            {/if}
            <div class="form-group col-md-4 col-6">
                <label>Eind datum voor registartie</label>
                <input type="datetime-local" class="form-control" name="Event_Registration_End_Timestamp" value="{$aEvent.Event_Registration_End_Timestamp|date_format:"%Y-%m-%dT%H:%M"}" required>
            </div>
            <div class="col-12">
                <hr/>
                <h6>Zichtbaarheid</h6>
            </div>
            <div class="form-group col-12">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="customCheck1" name="Event_IsActive"{if $aEvent.Event_IsActive === 1} checked{/if}>
                    <label class="custom-control-label" for="customCheck1">Inschrijving mogelijk</label>
                </div>
            </div>
            <div class="form-group col-12">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="customCheck2" name="Event_isVisible"{if $aEvent.Event_IsVisible === 1} checked{/if}>
                    <label class="custom-control-label" for="customCheck2">Zichtbaar</label>
                </div>
            </div>
            <div class="form-group col-12">
                <input type="submit" class="btn btn-primary" value="Opslaan">
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" id="tickets">
            <div class="card-header">
                <h2 class="card-title">Soorten tickets</h2>
                <div class="card-tools">

                </div>
            </div>
            <div class="card-body table-responsive">
                {$sTicketsCrud}
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Evenement verwijderen</h2>
            </div>
            <div class="card-body">
                {if $bHasEventTicketsSold}
                    <div class="alert alert-info">
                        Een evenement kan niet verwijdert worden wanneer er inschrijvingen zijn.
                    </div>
                {else}
                    <form id="delete-event" action="/{$aArura.dir}/api/shop/event.php?type=delete-event" method="post">
                        <input type="hidden" name="Event_Id" value="{$aEvent.Event_Id}" required>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                {/if}
            </div>
        </div>
    </div>
</div>