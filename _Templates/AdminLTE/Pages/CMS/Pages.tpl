{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Pagina's</li>
{/block}

{block content}
    <div class="card card-primary">
        <header class="card-header">
            <div class="card-tools">
                <div class="btn-group">
                    <button data-toggle="modal" data-target="#createModal" class="btn btn-primary"><i class="fas fa-plus"></i></button>
                </div>

            </div>
            <h2 class="card-title">Pagina's</h2>
        </header>
        <div class="card-body table-responsive" style="display: block;">
            <table class="table Arura-Table">
                <thead>
                <tr>
                    <th>Titel</th>
                    <th>Url</th>
                    <th>Zichtbaarheid</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {foreach $Pages as $aCMSPage}
                    <tr>
                        <td>{$aCMSPage->getTitle()}</td>
                        <td><a href="{$aCMSPage->getUrl()}" target="_blank">{$aCMSPage->getUrl()}</a></td>
                        <td>
                            {if $aCMSPage->getVisible()}
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
                                <a class="btn btn-secondary" href="/dashboard/content/pagina/{$aCMSPage->getId()}/instellingen" >
                                    <i class="fas fa-cog"></i>
                                </a>
                                <a href="/dashboard/content/pagina/{$aCMSPage->getId()}/analytics" class="btn btn-secondary">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                                <a href="/dashboard/content/pagina/{$aCMSPage->getId()}/content" class="btn btn-primary">
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

    <div style="display: none">
        <div class="template-pages-btns">

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
                    {$createForm}
                </div>
            </div>
        </div>
    </div>
{/block}

{block JsPage}
    <script>
        {if $createFormError}
            $("#createModal").modal("show");
        {/if}
    </script>
{/block}