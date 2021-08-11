{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/arura/webhook/">Webhooks</a></li>
    <li class="breadcrumb-item active">Webhook</li>
{/block}
{block content}
    <div class="card card-primary bg-secondary">
        <div class="card-body">
            {$form}
            {$webhook->getDeleteForm()}
        </div>
    </div>



{/block}
