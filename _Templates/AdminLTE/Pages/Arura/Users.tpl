{extends "../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item active">Gebruikers</li>
{/block}
{block content}
    <div class="card card-primary">
        <header class="card-header">
            <div class="card-tools">
                <button class="btn btn-primary" onclick="Users.Users.Create()">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <h2 class="card-title">Gebruikers</h2>
        </header>
        <div class="card-body table-responsive">
            <table class="table Arura-Table">
                <thead>
                <tr>
                    <th>
                        Gebruikersnaam
                    </th>
                    <th>
                        Voornaam
                    </th>
                    <th>
                        Achternaam
                    </th>
                    <th>
                        Email
                    </th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {foreach $aUsers as $aUser}
                    <tr>
                        <td>{$aUser.User_Username}</td>
                        <td>{$aUser.User_Firstname}</td>
                        <td>{$aUser.User_Lastname}</td>
                        <td>{$aUser.User_Email}</td>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-primary" href="/dashboard/arura/user/{$aUser.User_Id}"><i class="fas fa-pen"></i></a>
                            </div>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>

    <div class="card card-secondary">
        <header class="card-header">
            <h2 class="card-title">Sessies</h2>
        </header>
        <div class="card-body table-responsive">
            <table class="table Arura-Table">
                <thead>
                <tr>
                    <th>
                        Voornaam
                    </th>
                    <th>
                        Achternaam
                    </th>
                    <th>
                        Tijd laatste actie
                    </th>
                </tr>
                </thead>
                <tbody>
                {foreach $aSessions as $aSession}
                    <tr>
                        <td>{$aSession.User_Firstname}</td>
                        <td>{$aSession.User_Lastname}</td>
                        <td>{$aSession.Session_Last_Active|date_format:"%H:%M %d-%m-%Y"}</td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal modal-user-create" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gebruiker toevoegen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-row" method="post">
                        <input type="hidden" value="create-user" name="type">
                        <div class="form-group col-6">
                            <label class="control-label" for="user-username">Gebruikersnaam</label>
                            <input type="text" name="User_Username" class="form-control" required id="user-username">
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group col-6">
                            <label>Email</label>
                            <input type="email" name="User_Email" class="form-control" required>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group col-6">
                            <label>Voornaam</label>
                            <input type="text" name="User_Firstname" class="form-control" required>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group col-6">
                            <label>Achternaam</label>
                            <input type="text" name="User_Lastname" class="form-control" required>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group col-6">
                            <label>Wachtwoord</label>
                            <input type="password" name="User_Password_1" class="form-control" id="Create_User_Password_1" required>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group col-6">
                            <label>Wachtwoord (herhaling)</label>
                            <input type="password" name="User_Password_2" class="form-control" required>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-6">
                            <input type="submit" class="btn btn-success modal-confirm" value="Aanmaken">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div style="display: none">
        <div class="template-user-edit">
            <form class="form-row table-panel" method="post">
                <input type="hidden" value="save-user" name="type">
                <input type="hidden" name="User_Id">
                <div class="form-group col-6">
                    <label class="control-label" for="user-username">Gebruikersnaam</label>
                    <input type="text" name="User_Username" class="form-control" required>
                    <div class="help-block with-errors"></div>
                </div>
                <div class="form-group col-6">
                    <label>Email</label>
                    <input type="email" name="User_Email" class="form-control" required>
                    <div class="help-block with-errors"></div>
                </div>
                <div class="form-group col-6">
                    <label>Voornaam</label>
                    <input type="text" name="User_Firstname" class="form-control" required>
                    <div class="help-block with-errors"></div>
                </div>
                <div class="form-group col-6">
                    <label>Achternaam</label>
                    <input type="text" name="User_Lastname" class="form-control" required>
                    <div class="help-block with-errors"></div>
                </div>
                <div class="form-group col-6">
                    <label>Wachtwoord</label>
                    <input type="password" name="User_Password_1" class="form-control" id="User_Password_1">
                    <div class="help-block with-errors"></div>
                </div>
                <div class="form-group col-6">
                    <label>Wachtwoord (herhaling)</label>
                    <input type="password" name="User_Password_2" class="form-control" data-match="#User_Password_1">
                    <div class="help-block with-errors"></div>
                </div>
                <div class="col-6">
                    <input type="submit" class="btn btn-success" value="Opslaan">
                </div>
            </form>
        </div>
        <div class="template-user-rolles">
            <div class="table-card">
                <div class="row">
                    <div class="col-12">
                        <button class="btn btn-primary btn-role-add">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="col-12 roles">

                    </div>
                </div>
            </div>
        </div>

        <div class="template-user-role-details">
            <div class="row">
                <div class="col-8">
                    <span class="title"></span>
                </div>
                <div class="col-4">
                    <button class='btn btn-danger btn-role-delete'>
                        <i class='fas fa-trash-alt'></i>
                    </button>
                </div>
            </div>
        </div>



        <div class="template-user-edit-btns">
            <div>
                <div class="btn-group btn-user-menu">
                    <button class='btn btn-primary' onclick="Users.Users.AltUser($(this))">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button class='btn btn-primary' onclick="Users.Users.AltRoles($(this))">
                        <i class="fas fa-user-tag"></i>
                    </button>
                    <button class='btn btn-danger' onclick="Users.Users.Delete($(this))">
                        <i class='fas fa-trash-alt'></i>
                    </button>
                </div>

                <div class="btn-group btn-user-menu-close" style="display: none" onclick="Users.Users.CloseMenu($(this))">
                    <button class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="template-user-create">

        </div>
    </div>
{/block}