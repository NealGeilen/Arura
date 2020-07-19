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
            <table class="table pages-overvieuw">
                <thead>
                <tr>
                    <th>Titel</th>
                    <th>Url</th>
                    <th>Zichtbaarheid</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div style="display: none">
        <div class="template-pages-btns">
            <div class="btn-group">
                <a class="btn btn-secondary" href="" page="instellingen">
                    <i class="fas fa-cog"></i>
                </a>
                <a href="" class="btn btn-primary" page="content">
                    <i class="fas fa-pen"></i>
                </a>
                <button type="button" class="btn btn-danger" onclick="Pages.Delete($(this))" >
                    <i class="far fa-trash-alt"></i>
                </button>
            </div>
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