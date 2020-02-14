{extends "../../index.tpl"}
{block content}
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Aanmaken</h2>
            <div class="card-tools">
                <div class="btn-group btn-group-sm">
                    <a class="btn btn-primary" href="/{$aArura.dir}/winkel/evenementen/beheer"><i class="fas fa-long-arrow-alt-left"></i></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="form-row" action="{$smarty.SERVER.REQUEST_URI}" method="post">
                <div class="col-12">
                    <h6>Basis</h6>
                </div>
                <div class="form-group col-md-4 col-6">
                    <label>Naam</label>
                    <input type="text" class="form-control" name="Event_Name" value="" required>
                </div>
                <div class="form-group col-md-4 col-6">
                    <label>Slug</label>
                    <input type="text" class="form-control" name="Event_Slug" value="" required>
                </div>
                <div class="form-group col-md-4 col-6">
                    <label>Locatie</label>
                    <input type="text" class="form-control" name="Event_Location" value="" required>
                </div>
                <div class="form-group col-12">
                    <label>Omschrijving</label>
                    <textarea class="richtext" name="Event_Description"></textarea>
                </div>
                <div class="form-group col-md-6 col-6">
                    <label>Start datum</label>
                    <input type="datetime-local" class="form-control" name="Event_Start_Timestamp" value="" required>
                </div>
                <div class="form-group col-md-6 col-6">
                    <label>Eind datum</label>
                    <input type="datetime-local" class="form-control" name="Event_End_Timestamp" value="" required>
                </div>
                <div class="col-12">
                    <hr/>
                    <h6>Details</h6>
                </div>
                <div class="form-group col-md-6 col-6">
                    <label>Banner evenement</label>
                    <input type="text" class="form-control file-selector" name="Event_Banner" value="" required>
                </div>
                <div class="form-group col-md-6 col-6">
                    <label>Organizator</label>
                    <select class="form-control" value="{$aUser.User_Id}" name="Event_Organizer_User_Id" required>
                        {foreach from=$aUsers item=user}
                            <option value="{$user.User_Id}">{$user.User_Username} | {$user.User_Email}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="form-group col-md-6 col-6">
                    <label>Capaciteit</label>
                    <input type="number" class="form-control" name="Event_Capacity" value="" min="0" required>
                </div>
                <div class="form-group col-md-4 col-6">
                    <label>Eind datum voor registartie</label>
                    <input type="datetime-local" class="form-control" name="Event_Registration_End_Timestamp" value="" required>
                </div>
                <div class="form-group col-12">
                    <input type="submit" class="btn btn-primary" name="" value="Aanmaken">
                </div>
            </form>
        </div>
    </div>
{/block}