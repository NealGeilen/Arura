{extends "../../index.tpl"}
{block content}
    <div class="row">
        {foreach from=$aSettings key=$Key item=aSettingsGroup}
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h2 class="card-title text-capitalize">{$Key}</h2>
                        <div class="card-tools">
                            <button class="btn btn-primary" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <form class="settings-form" autocomplete="off">
                            <div class="table-responsive">
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
                            </div>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i>
                                </button>
                                <button type="reset" class="btn btn-info">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        {/foreach}
    </div>

{/block}