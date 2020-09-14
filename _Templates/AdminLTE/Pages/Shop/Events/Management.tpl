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
            <table class="table Arura-Table table-hover">
                <thead>
                <tr>
                    <th>Naam</th>
                    <th>Status</th>
                    <th>Slug</th>
                    <th>Tijd</th>
                    <th>Aanmeldingen</th>
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
                {foreach from=$Events key=$iKey item=Event}
                    <tr {if !$Event->canEdit() || $Event->isCanceled()}disabled{/if}>
                        <td>{$Event->getName()}</td>
                        <td><span class="badge badge-info">{$Event->getStatus()}</span></td>
                        <td>{$Event->getSlug()}</td>
                        <td data-order="{$Event->getStart()->format("U")}">{$Event->getStart()->format("U")|date_format:"%H:%M %d-%m-%y"} t/m {$Event->getEnd()->format("U")|date_format:"%H:%M %d-%m-%y"}</td>
                        <td>{$Event->getAmountSignIns()}</td>
                        <td class="btn-group btn-group">
                            <a class="btn btn-primary" href="/{$aArura.dir}/winkel/evenement/{$Event->getId()}">
                                {if !$Event->canEdit() || $Event->isCanceled()}
                                    <i class="fas fa-eye"></i>
                                    {else}
                                    <i class="fas fa-info"></i>
                                {/if}
                            </a>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/block}