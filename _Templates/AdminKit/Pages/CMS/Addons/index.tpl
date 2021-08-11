{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Addons</li>
{/block}

{block pageactions}
    <a href="/dashboard/content/addons/create" class="btn btn-primary"><i class="fas fa-plus"></i></a>
    <button class="btn btn-secondary" data-toggle="modal" data-target="#ImportAddon">
        <i class="fas fa-file-import"></i>
    </button>
{/block}

{block content}
    <div class="mb-2">
        <div class="alert alert-primary alert-outline alert-dismissible" role="alert">
            <div class="alert-message">
                {$AddonCachForm}
            </div>
        </div>
    </div>
    <div class="card card-primary">
        <header class="card-header">
            <h2 class="card-title">Addons</h2>
        </header>
        <div class="card-body table-responsive" style="display: block;">
            <table class="table Arura-Table">
                <thead>
                <tr>
                    <th>Naam</th>
                    <th>Actief</th>
                    <th>Meerderen velden</th>
                    <th>Soort</th>
                    <th>Acties</th>
                </tr>
                </thead>
                <tbody>
                {foreach $Addons as $Addon}
                    <tr>
                        <td>{$Addon.Addon_Name}</td>
                        <td>
                            {if $Addon.Addon_Active}
                                <div class="badge bg-success">
                                    <i class="fas fa-check"></i>
                                </div>
                            {else}
                                <div class="badge bg-danger">
                                    <i class="fas fa-times"></i>
                                </div>
                            {/if}
                        </td>
                        <td>
                            {if $Addon.Addon_Multipel_Values}
                                <div class="badge bg-success">
                                    <i class="fas fa-check"></i>
                                </div>
                            {else}
                                <div class="badge bg-danger">
                                    <i class="fas fa-times"></i>
                                </div>
                            {/if}
                        </td>
                        <td>
                            {$Addon.Addon_Type|ucfirst}
                        </td>
                        <td>
                            <div class="btn-group float-md-end">
                                <a  class="btn btn-primary" href="/dashboard/content/addon/{$Addon.Addon_Id}/layout">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <a class="btn btn-secondary" href="/dashboard/content/addon/{$Addon.Addon_Id}/settings">
                                    <i class="fas fa-cog"></i>
                                </a>
                                <a  class="btn btn-secondary" href="/dashboard/content/addon/{$Addon.Addon_Id}/export" target="_blank">
                                    <i class="fas fa-file-export"></i>
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
    <div class="modal fade" id="ImportAddon"  role="dialog" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Addon importeren</h5>
                </div>
                <div class="modal-body">
                    <form action="/dashboard/content/addons" id="file-upload" class="dropzone"></form>
                </div>
            </div>
        </div>
    </div>
{/block}

{block JsPage}
    <script>
        $(".dropzone").dropzone({});
    </script>
{/block}