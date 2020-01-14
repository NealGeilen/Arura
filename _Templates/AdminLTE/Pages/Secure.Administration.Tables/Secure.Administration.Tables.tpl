<div class="card">
    <div class="card-header">
        <h2 class="card-title">Administraties beschikbaar</h2>
        <div class="card-tools">
            {if $aPermissions.SECURE_ADMINISTRATION_CREATE}
                <a class="btn btn-primary btn-sm" href="/{$aArura.dir}/administration?c">
                    <i class="fas fa-plus"></i>
                </a>
            {/if}
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table Arura-Table">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Rol</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aTables key=$iKey item=aTable}
            <tr>
                <td>{$aTable.Table_Name}</td>
                <td>{if $aTable.Table_Owner_User_Id == $aUser.User_Id}Eigenaar{else}Gebruiker{/if}</td>
                <td>
                    <div class="btn-group text-white btn-group-sm">
                        {if $aTable.Table_Owner_User_Id == $aUser.User_Id}<a class="btn btn-primary" href="/{$aArura.dir}/administration?s={$aTable.Table_Id}"><i class="fas fa-cogs"></i></a>{/if}
                        <a class="btn btn-secondary" href="/{$aArura.dir}/administration?t={$aTable.Table_Id}"><i class="fas fa-pen"></i></a>
                    </div>
                </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>