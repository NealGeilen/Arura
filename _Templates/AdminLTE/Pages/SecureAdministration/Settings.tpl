{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/administration">Beveiligde administartie</a></li>
    <li class="breadcrumb-item active">Instellingen: {$aTable.Table_Name}</li>
{/block}

{block content}
    <script>
        _TABLE_ID = {$aTable.Table_Id}
    </script>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h2 class="card-title">Gebruikers - {$aTable.Table_Name}</h2>
                    <div class="card-tools">
                        <div class="btn-group">
                            <a class="btn btn-primary" href="/{$aArura.dir}/administration"><i class="fas fa-long-arrow-alt-left"></i></a>
                            <a class="btn btn-primary" href="/{$aArura.dir}/administration/{$aTable.Table_Id}/edit"><i class="fas fa-pen"></i></a>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table Arura-Table">
                        <thead>
                        <tr>
                            <th>Naam gebruiker</th>
                            <th>Recht</th>
                            <th><button class="btn btn-primary" onclick="addUser()"><i class="fas fa-plus"></i></button></th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$aUsersTable item=user}
                            <tr>
                                <td>{$user.User_Username}</td>
                                <td>
                                    <div class="btn-group btn-group-toggle crud-rights" data-toggle="buttons" user-id="{$user.User_Id}">
                                        <label onclick="updateRights({$user.User_Id})" class="btn btn-secondary {if $user.Share_Permission & 1}active{/if}">
                                            <input type="checkbox"  value="1" autocomplete="off" {if $user.Share_Permission & 1}checked{/if}>Lezen
                                        </label>
                                        <label onclick="updateRights({$user.User_Id})" class="btn btn-secondary {if $user.Share_Permission & 2}active{/if}">
                                            <input type="checkbox"  value="2" autocomplete="off" {if $user.Share_Permission & 2}checked{/if}>Aanmaken
                                        </label>
                                        <label onclick="updateRights({$user.User_Id})" class="btn btn-secondary {if $user.Share_Permission & 4}active{/if}">
                                            <input type="checkbox" value="4" autocomplete="off" {if $user.Share_Permission & 4}checked{/if}>Aanpassen
                                        </label>
                                        <label onclick="updateRights({$user.User_Id})" class="btn btn-secondary {if $user.Share_Permission & 8}active{/if}">
                                            <input type="checkbox"  value="8" autocomplete="off" {if $user.Share_Permission & 8}checked{/if}>Verwijderen
                                        </label>
                                        <label onclick="updateRights({$user.User_Id})" class="btn btn-secondary {if $user.Share_Permission & 16}active{/if}">
                                            <input type="checkbox" value="16" autocomplete="off" {if $user.Share_Permission & 16}checked{/if}>Exporteren
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-danger" onclick="removeUser({$user.User_Id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-secondary">
                <div class="card-header">
                    <h2 class="card-title">Instelling - {$aTable.Table_Name}</h2>
                </div>
                <div class="card-body">
                    <form class="form-update-table" method="post">
                        <input value="{$aTable.Table_Id}" type="hidden" name="Table_Id">
                        <input value="save-table" type="hidden" name="type">
                        <div class="form-row">
                            <div class="col-6 form-group">
                                <label>Naam</label>
                                <input type="text" class="form-control" value="{$aTable.Table_Name}" name="Table_Name">
                            </div>
                            <div class="col-6 form-group">
                                <label>Eigenaar</label>
                                <select class="form-control" onclick="warning()" onfocus="warning()" name="Table_Owner_User_Id">
                                    <option value="{$aUser.User_Id}">Huidige: <bold>{$aUser.User_Username}</bold></option>
                                    {foreach from=$aUsers item=$user}
                                        <option value="{$user.User_Id}">{$user.User_Username}</option>
                                    {/foreach}
                                </select>
                                <div class="alert alert-warning" style="display: none">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    Als je de eigenaar wijzigt heb je geen toegang meer tot deze gegevens.
                                </div>
                            </div>
                        </div>
                        <input type="submit" value="Opslaan" class="btn btn-primary">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-secondary">
                <div class="card-header">
                    <h2 class="card-title">Verwijder - {$aTable.Table_Name}</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <span>Wanneer je de database verwijdert gaat alle data verloren!!</span>
                    </div>
                    <button class="btn btn-danger" onclick="dumpDB()">Verwijder {$aTable.Table_Name}</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal" tabindex="-1" role="dialog" id="add-user">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gebruiker toevoegen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select class="form-control" table-id="{$aTable.Table_Id}">
                        {foreach from=$aUsers item=$user}
                            <option value="{$user.User_Id}">{$user.User_Username}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary submit">Toevoegen</button>
                </div>
            </div>
        </div>
    </div>
{/block}

