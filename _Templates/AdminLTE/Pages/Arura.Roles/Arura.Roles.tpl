<section class="card">
    <header class="card-header">
        <div class="card-tools">
            <button class="btn btn-primary btn-sm" onclick="Roles.Roles.Create()">
                <i class="fas fa-plus"></i>
            </button>
        </div>
        <h2 class="card-title">Rollen</h2>
    </header>
    <div class="card-body table-responsive">
        <table id="rolles-overview" class="table">
            <thead>
            <tr>
                <th>
                    ID
                </th>
                <th>
                    Naam
                </th>
                <th>
                    Rechten
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
        <h2 class="card-title">Rechten</h2>
    </header>
    <div class="card-body table-responsive" style="display: block;">
        <table class="table">
            <thead>
            <tr>
                <th>Id</th>
                <th>Naam</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$aRights key=$iKey item=aRight}
            <tr>
                <td>{$aRight.Right_Id}</td>
                <td>{$aRight.Right_Name}</td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</section>
<div class="modal modal-role-create" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rol toevoegen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-row" method="post" action="/{$aArura.dir}/{$aArura.api}/user/roles.php">
                    <input type="hidden" value="create-role" name="type">
                    <div class="form-group col-12">
                        <label class="control-label">Naam</label>
                        <input type="text" name="Role_Name" class="form-control" required">
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
    <div class="template-role-edit">
        <form class="form-row table-card" method="post" action="/{$aArura.dir}/{$aArura.api}/roles.php">
            <input type="hidden" value="save-role" name="type">
            <input type="hidden" name="Role_Id">
            <div class="form-group col-12">
                <label class="control-label">Naam</label>
                <input type="text" name="Role_Name" class="form-control" required>
                <div class="help-block with-errors"></div>
            </div>
            <div class="col-6">
                <input type="submit" class="btn btn-success" value="Opslaan">
            </div>
        </form>
    </div>
    <div class="template-roles-rights">
        <div class="table-card">
            <div class="row">
                <div class="col-12">
                    <button class="btn btn-primary btn-sm btn-right-add">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="col-12 rights">

                </div>
            </div>
        </div>
    </div>

    <div class="template-role-rights-details">
        <div class="row">
            <div class="col-8">
                <span class="title"></span>
            </div>
            <div class="col-4">
                <button class='btn btn-danger btn-sm btn-right-delete'>
                    <i class='fas fa-trash-alt'></i>
                </button>
            </div>
        </div>
    </div>



    <div class="template-roles-edit-btns">
        <div>
            <div class="btn-group btn-group-sm btn-role-menu">
                <button class='btn btn-primary' onclick="Roles.Roles.AltRole($(this))">
                    <i class="fas fa-pen"></i>
                </button>
                <button class='btn btn-primary' onclick="Roles.Roles.AltRights($(this))">
                    <i class="fas fa-user-tag"></i>
                </button>
            </div>

            <div class="btn-group btn-group-sm btn-role-menu-close" style="display: none" onclick="Roles.Roles.CloseMenu($(this))">
                <button class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <button class='btn btn-danger btn-sm' onclick="Roles.Roles.Delete($(this))">
                <i class='fas fa-trash-alt'></i>
            </button>
        </div>
    </div>

    <div class="template-role-create">

    </div>



</div>
