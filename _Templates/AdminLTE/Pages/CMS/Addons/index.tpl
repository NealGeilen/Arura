{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Addons</li>
{/block}

{block content}
    <div class="card card-primary">
        <header class="card-header">
            <div class="card-tools">
                <div class="btn-group">
                    <a href="/dashboard/content/addons/create" class="btn btn-primary"><i class="fas fa-plus"></i></a>
                </div>

            </div>
            <h2 class="card-title">Addons</h2>
        </header>
        <div class="card-body table-responsive" style="display: block;">
            <table class="table Arura-Table">
                <thead>
                <tr>
                    <th>Naam</th>
                    <th>Actief</th>
                    <th>Meerderen velden</th>
                    <th>Custom</th>
                    <th>Acties</th>
                </tr>
                </thead>
                <tbody>
                {foreach $Addons as $Addon}
                    <tr>
                        <td>{$Addon.Addon_Name}</td>
                        <td>
                            {if $Addon.Addon_Active}
                                <div class="badge badge-success">
                                    <i class="fas fa-check"></i>
                                </div>
                            {else}
                                <div class="badge badge-danger">
                                    <i class="fas fa-times"></i>
                                </div>
                            {/if}
                        </td>
                        <td>
                            {if $Addon.Addon_Multipel_Values}
                                <div class="badge badge-success">
                                    <i class="fas fa-check"></i>
                                </div>
                            {else}
                                <div class="badge badge-danger">
                                    <i class="fas fa-times"></i>
                                </div>
                            {/if}
                        </td>
                        <td>
                            {if $Addon.Addon_Custom}
                                <div class="badge badge-success">
                                    <i class="fas fa-check"></i>
                                </div>
                            {else}
                                <div class="badge badge-danger">
                                    <i class="fas fa-times"></i>
                                </div>
                            {/if}
                        </td>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-secondary" href="/dashboard/content/addon/{$Addon.Addon_Id}/settings">
                                    <i class="fas fa-cog"></i>
                                </a>
                                <a  class="btn btn-primary" href="/dashboard/content/addon/{$Addon.Addon_Id}/layout">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </div>
                        </td>

                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="createModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pagina aanmaken</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
{/block}

{block JsPage}
    <script>
    </script>
{/block}