
{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Beveiligde administartie</li>
{/block}

{block content}
    <div class="row">
        {if $aPermissions.SECURE_ADMINISTRATION_CREATE}
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h2 class="card-title">Administartie aanmaken</h2>
                    </div>
                    <div class="card-body">
                        <p>JSON bestand vereist</p>
                        <a class="btn btn-primary" href="/{$aArura.dir}/administration/create">
                            <i class="fas fa-plus"></i> Aanmaken
                        </a>
                    </div>
                </div>
            </div>
        {/if}
        {foreach from=$aTables key=$iKey item=aTable}
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card {if $aTable.Table_Owner_User_Id == $aUser.User_Id}card-primary{else}card-secondary{/if} ">
                    <div class="card-header">
                        <h2 class="card-title">{$aTable.Table_Name}</h2>
                        <div class="card-tools">
                            <i class="fas fa-database"></i> {$aTable.ROWCOUNT}
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            {if $aTable.Table_Owner_User_Id == $aUser.User_Id}Eigenaar{else}Gebruiker{/if}
                        </p>
                        <div class="btn-group">
                            {if $aTable.Table_Owner_User_Id == $aUser.User_Id}
                                <a class="btn btn-primary" href="/{$aArura.dir}/administration/{$aTable.Table_Id}/settings">
                                    <i class="fas fa-cogs"></i> Instellingen
                                </a>
                            {/if}
                            <a class="btn btn-secondary" href="/{$aArura.dir}/administration/{$aTable.Table_Id}/edit">
                                <i class="fas fa-pen"></i> Bewerken
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            {foreachelse}
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h2 class="card-title">Geen administartie</h2>
                    </div>
                    <div class="card-body">
                        <p>
                            Er is op het moment geen administartie beschikbaar
                        </p>
                    </div>
                </div>
            </div>
        {/foreach}
    </div>
{/block}