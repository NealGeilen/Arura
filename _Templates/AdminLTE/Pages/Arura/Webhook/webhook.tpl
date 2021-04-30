{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item active">Webhooks</li>
{/block}
{block content}
    <div class="card card-primary">
        <div class="card-header">
            <div class="card-tools">
                <button class="btn btn-primary"  data-toggle="modal" data-target="#createWebhook">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="Arura-Table table">
                <thead>
                <tr>
                    <th>Trigger</th>
                    <th>Url</th>
                    <th>Actie</th>
                </tr>
                </thead>
                <tbody>
                {foreach $Webhooks as $Webhook}
                    <tr>
                        <td>{$Triggers[$Webhook->getTrigger()]}</td>
                        <td>{$Webhook->getUrl()}</td>
                        <td>
                            <a class="btn btn-primary" href="/dashboard/arura/webhook/{$Webhook->getId()}/edit">
                                <i class="fas fa-pen"></i>
                            </a>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="createWebhook"  role="dialog" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {$createForm->startForm()}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Webhook aanmaken</h5>
                </div>
                <div class="modal-body">
                    {$createForm->getControl("Webhook_Trigger")}
                    {$createForm->getControl("Webhook_Url")}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    {$createForm->getControl("submit")}
                </div>
                {$createForm->endForm()}
            </div>
        </div>
    </div>
{/block}


{block JsPage}
    <script>
        {if $createForm->hasErrors()}
        $("#createWebhook").modal("show");
        {/if}
    </script>
{/block}