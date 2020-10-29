{extends "../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item active">Url verkleinen</li>
{/block}


{block content}
    <div class="card card-primary">
        <header class="card-header">
            <div class="card-tools">
                <button class="btn btn-primary" data-toggle="modal" data-target="#createRedirect">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <h2 class="card-title">Urls</h2>
        </header>
        <div class="card-body table-responsive">
            <table class="table Arura-Table">
                <thead>
                <tr>
                    <th>
                        Url
                    </th>
                    <th>
                        Doel
                    </th>
                    <th>
                    </th>
                </tr>
                </thead>
                <tbody>
                {foreach $Urls as $Url}
                    <tr>
                        <td><a href="/r/{$Url->getToken()}" target="_blank">{$aWebsite.url}/r/{$Url->getToken()}</a></td>
                        <td><a href="{$Url->getDirection()}" target="_blank">{$Url->getDirection()}</a></td>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-primary text-white" href="/dashboard/redirects/shorten/{$Url->getToken()}/analytics">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                                <button class="btn btn-secondary" onclick="showQRCode('{$Url->getToken()}')">
                                    <i class="fas fa-qrcode"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteRedirect('{$Url->getToken()}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="createRedirect"  role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {$createForm->startForm()}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Url aanmaken</h5>
                </div>
                <div class="modal-body">
                    {$createForm->getControl("Url_Direction")}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    {$createForm->getControl("submit")}
                </div>
                {$createForm->endForm()}
            </div>
        </div>
    </div>

    <div class="modal fade" id="QR-Redirect"  role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">QR Code</h5>
                </div>
                <div class="modal-body">
                    <img src="" alt="qr-Code" style="margin: 0 auto; display: block">
                </div>
                <div class="modal-footer">
                    <a href="" class="btn btn-secondary" download="QR-Code.png"><i class="fas fa-download"></i></a>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Oke</button>
                </div>
            </div>
        </div>
    </div>
{/block}

{block JsPage}
    <script>
        {if $createForm->hasErrors()}
        $("#createRedirect").modal("show");
        {/if}
    </script>
{/block}