<section class="card">
    <header class="card-header">
        <div class="card-tools">
            <a href="javascript:Users.Users.Create()" class="far fa-plus-square btn btn-tool"></a>
        </div>
        <h2 class="card-title">Gebruikers</h2>
    </header>
    <div class="card-body table-responsive" style="display: block;">
        <table id="users-overview" class="table">
            <thead>
            <tr>
                <th>
                    ID
                </th>
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
                <th>
                    Rollen
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <header class="card-header">
        <h2 class="card-title">Sessies</h2>
    </header>
    <div class="card-body table-responsive" style="display: block;">
        <table class="table" id="sessions-overview">
            <thead>
            <tr>
                <th>
                    Sessie Id
                </th>
                <th>
                    Gebruikersnaam
                </th>
                <th>
                    Tijd laatste actie
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</section>
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
                <form class="form-row" method="post" action="/{$aArura.dir}/{$aArura.api}/user/manage.php">
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
        <form class="form-row table-panel" method="post" action="/{$aArura.dir}/{$aArura.api}/user/manage.php">
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
                    <button class="btn btn-primary btn-sm btn-role-add">
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
                <button class='btn btn-danger btn-sm btn-role-delete'>
                    <i class='fas fa-trash-alt'></i>
                </button>
            </div>
        </div>
    </div>



    <div class="template-user-edit-btns">
        <div>
            <div class="btn-group btn-group-sm btn-user-menu">
                <button class='btn btn-primary' onclick="Users.Users.AltUser($(this))">
                    <i class="fas fa-pen"></i>
                </button>
                <button class='btn btn-primary' onclick="Users.Users.AltRoles($(this))">
                    <i class="fas fa-user-tag"></i>
                </button>
            </div>

            <div class="btn-group btn-group-sm btn-user-menu-close" style="display: none" onclick="Users.Users.CloseMenu($(this))">
                <button class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <button class='btn btn-danger btn-sm' onclick="Users.Users.Delete($(this))">
                <i class='fas fa-trash-alt'></i>
            </button>
        </div>
    </div>

    <div class="template-user-create">

    </div>
</div>