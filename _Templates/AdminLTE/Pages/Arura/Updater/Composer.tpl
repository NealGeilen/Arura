{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Updaten</li>
{/block}
{block content}
    {assign var="tabsType" value="package"}
    {include file="./tabs.tpl"}
    <div class="card card-primary arura-updater">
        <div class="card-header">
            <h2 class="card-title">Composer</h2>
            <div class="card-tools">
                <div class="btn-group">
                    <button class="btn btn-primary" data-card-widget="collapse" ><i class="fas fa-minus"></i></button>
                </div>
            </div>
        </div>
        <div class="card-body bg-primary">
            <ul class="list-group text-dark">

            </ul>
        </div>
        <div class="overlay">
            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
        </div>
    </div>
{/block}