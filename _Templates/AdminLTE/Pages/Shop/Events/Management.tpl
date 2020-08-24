{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Evenementen</li>
{/block}

{block content}
    <div class="card card-primary">
        <div class="card-header">
            <h2 class="card-title">Evenementen</h2>
            <div class="card-tools">
                <button class="btn btn-primary" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="table Arura-Table">
                <thead>
                <tr>
                    <th>Naam</th>
                    <th>Slug</th>
                    <th>Tijd</th>
                    <th>
                        {if $aPermissions.SHOP_EVENTS_MANAGEMENT}
                        <div class="btn-group">
                            <a class="btn btn-primary" href="/{$aArura.dir}/winkel/evenementen/aanmaken"><i class="fas fa-plus"></i></a>
                        </div>
                        {/if}
                    </th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aEvents key=$iKey item=aEvent}
                    <tr>
                        <td>{$aEvent.Event_Name}</td>
                        <td>{$aEvent.Event_Slug}</td>
                        <td>{$aEvent.Event_Start_Timestamp|date_format:"%H:%M %d-%m-%y"} t/m {$aEvent.Event_End_Timestamp|date_format:"%H:%M %d-%m-%y"}</td>
                        <td class="btn-group btn-group">
                            <a class="btn btn-primary" href="/{$aArura.dir}/winkel/evenement/{$aEvent.Event_Id}"><i class="fas fa-pen"></i></a>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/block}