{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Updaten</li>
{/block}
{block pageactions}

{/block}
{block content}
    {assign var="tabsType" value="package"}
    {include file="./tabs.tpl"}
    <div class="card bg-primary arura-updater">
        <div class="card-header">
            <h2 class="card-title">Composer</h2>
        </div>
        <div class="card-body">
            <ul class="list-group text-dark">

            </ul>
        </div>
    </div>
{/block}