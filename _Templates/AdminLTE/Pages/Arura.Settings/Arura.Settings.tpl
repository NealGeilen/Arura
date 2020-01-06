<div class="row">
    {foreach from=$aSettings key=$Key item=aSettingsGroup}
    <div class="col-md-6">
        <section class="card">
            <div class="card-body table-responsive">
                <h2 class="text-capitalize">{$Key}</h2>
                <form class="settings-form" data-toggle="validator" autocomplete="off">
                    <table class="table Arura-Table">
                        <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Waarden</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$aSettingsGroup key=$iKey item=aSetting}
                        <tr>
                            <td>{$aSetting.Setting_Name}</td>
                            <td class="form-group">
                                {if $aSetting.Setting_Type === "file"}
                                <input type="text" class="form-control file-selector" file-type="img" plg="{$aSetting.Setting_Plg}" name="{$aSetting.Setting_Name}" value="{$aSetting.Setting_Value}"
                                       {if $aSetting.Setting_Required}
                                       required
                                       {/if}
                                >
                                {elseif $aSetting.Setting_Type === "checkbox"}
                                <input type="checkbox" plg="{$aSetting.Setting_Plg}" name="{$aSetting.Setting_Name}" {if $aSetting.Setting_Value}checked{/if}
                                {if $aSetting.Setting_Required}
                                required
                                {/if}
                                >
                                {else}
                                <input type="{$aSetting.Setting_Type}" class="form-control" plg="{$aSetting.Setting_Plg}" name="{$aSetting.Setting_Name}" value="{$aSetting.Setting_Value}"
                                       {if $aSetting.Setting_Required}
                                       required
                                       {/if}
                                >
                                {/if}
                                <div class="help-block with-errors"></div>
                            </td>
                        </tr>
                        {/foreach}
                        </tbody>
                    </table>
                    <input value="Opslaan {$Key}" type="submit" class="btn btn-primary btn-sm text-capitalize">
                </form>
            </div>
        </section>
    </div>
    {/foreach}
</div>
