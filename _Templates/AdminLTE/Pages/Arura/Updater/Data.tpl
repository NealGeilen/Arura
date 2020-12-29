{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Updaten</li>
{/block}
{block content}
    {assign var="tabsType" value="data"}
    {include file="./tabs.tpl"}
    <div class="card card-primary">
        <div class="card-header">
            <h2 class="card-title">Database</h2>
            <div class="card-tools">
                <div class="btn-group">
                    {if $aDBChanges !== [] }
                        <form action="{$smarty.server.REQUEST_URI}" method="post">
                            <input class="btn btn-secondary" name="reload" value="Wijzigingen doorvoeren" type="submit" onclick="startPageLoad()">
                        </form>
                    {/if}
                    <button class="btn btn-secondary" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
        </div>
        <div class="card-body bg-primary">
            {if $aDBChanges !== [] }
                <ul class="list-group">
                    {foreach $aDBChanges as $change}
                        <li class="list-group-item text-dark">{$change}</li>
                    {/foreach}
                </ul>
            {else}
                <div class="alert alert-warning">
                    Geen database wijzigingen
                </div>
            {/if}
        </div>
    </div>
{/block}