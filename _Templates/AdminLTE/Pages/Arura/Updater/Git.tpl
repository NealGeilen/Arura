{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Updaten</li>
{/block}
{block content}
    {assign var="tabsType" value="git"}
    {include file="./tabs.tpl"}
    <div class="card card-secondary">
        <div class="card-header">
            <h2 class="card-title">Frontend</h2>
            <div class="card-tools">
                <div class="btn-group">
                    {if $bGit}
                        <form action="{$smarty.server.REQUEST_URI}" method="post">
                            <input class="btn btn-info" name="gitpull" value="Update frontend" type="submit" onclick="startPageLoad()">
                            <input class="btn btn-danger" name="gitreset" value="Reset frontend" type="submit" onclick="startPageLoad()">
                        </form>
                    {/if}
                    <button class="btn btn-secondary" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
        </div>
        <div class="card-body bg-secondary">
            {if $bGit}
                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <th>Commit Id</th>
                            <td>{$LastCommit.commit}</td>
                        </tr>
                        <tr>
                            <th>Onderwerp</th>
                            <td>{$LastCommit.subject}</td>
                        </tr>
                        <tr>
                            <th>Auteur</th>
                            <td>{$LastCommit.author}</td>
                        </tr>
                        <tr>
                            <th>Datum</th>
                            <td>{$LastCommit.date|date_format:"%H:%M %d-%m-%Y"}</td>
                        </tr>

                    </table>
                </div>
                <div>
                    <ul class="list-group">
                        {foreach from=$Status item=$s}
                            {if $s !== ""}
                                <li class="list-group-item text-dark">{$s}</li>
                            {/if}
                        {/foreach}

                    </ul>
                </div>
            {else}
                <div class="alert alert-warning">Er is geen Git repo gevonden voor de frontend</div>
            {/if}
        </div>
    </div>
{/block}