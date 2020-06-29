{extends "../../index.tpl"}
{block content}
    <script>
        _TABLE_ID = {$aTable.Table_Id}
    </script>
    <div class="card card-primary">
        <div class="card-header">
            <h2 class="card-title">{$aTable.Table_Name}</h2>
            <div class="card-tools">
                <div class="btn-group">
                    <a class="btn btn-primary" href="/{$aArura.dir}/administration" title="Terug"><i class="fas fa-long-arrow-alt-left"></i></a>
                    {if $aTable.Table_Owner_User_Id == $aUser.User_Id}<a class="btn btn-primary" title="Instellingen" href="/{$aArura.dir}/administration/{$aTable.Table_Id}/settings"><i class="fas fa-cogs"></i></a>{/if}
                    {if $bCanExport}
                        <button class="btn btn-primary" title="Export" onclick="Export()"><i class="fas fa-file-export"></i></button>
                    {/if}
                </div>
            </div>
        </div>
        <div class="card-body table-responsive">
            {$sCrud}
        </div>
    </div>
{/block}