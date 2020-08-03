{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item active">Evenementen</li>
{/block}
{block content}
    <div class="card card-primary">
        <div class="card-header">
            <h2 class="card-title">Bestaande evenementen</h2>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped Arura-Table">
                <thead>
                <tr>
                    <th>Naam</th>
                    <th>Slug</th>
                    <th>Tijd</th>
                    <th>Aantal inschrijvingen</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aEvents key=$iKey item=aEvent}
                    <tr>
                        <td>{$aEvent.Event_Name}</td>
                        <td>{$aEvent.Event_Slug}</td>
                        <td>{$aEvent.Event_Start_Timestamp|date_format:"%H:%M %d-%m-%y"} t/m {$aEvent.Event_End_Timestamp|date_format:"%H:%M %d-%m-%y"}</td>
                        <td>{$aEvent.Amount}</td>
                        <td class="btn-group btn-group-sm">
                            <a class="btn btn-primary" href="/{$aArura.dir}/winkel/evenementen/tickets/{$aEvent.Event_Id}">Meer informatie</a>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/block}