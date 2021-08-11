{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Updaten</li>
{/block}
{block pageactions}
{if $aDBChanges !== [] }
    <form action="{$smarty.server.REQUEST_URI}" method="post">
        <input class="btn btn-secondary" name="reload" value="Wijzigingen doorvoeren" type="submit" onclick="startPageLoad()">
    </form>
{/if}
{/block}
{block content}
    {assign var="tabsType" value="data"}
    {include file="./tabs.tpl"}
    <div class="card card-primary">
        <div class="card-header">
            <h2 class="card-title">Database</h2>
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
                    <div class="alert-message">
                        Geen database wijzigingen
                    </div>
                </div>
            {/if}
        </div>
    </div>
{/block}