{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Updaten</li>
{/block}
{block content}
    <div class="container">
        <div class="card card-primary arura-updater">
            <div class="card-header">
                <h2 class="card-title">Composer</h2>
                <div class="card-tools">
                    <div class="btn-group">
                        <button class="btn btn-primary" data-card-widget="collapse" ><i class="fas fa-minus"></i></button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <ul class="list-group">

                </ul>
            </div>
            <div class="overlay">
                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>
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
            <div class="card-body">
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
                    <div class="table-responsive">
                        <table class="table">
                            {foreach from=$Status item=$s}
                                {if $s !== ""}
                                    <tr>
                                        <td>{$s}</td>
                                    </tr>
                                {/if}
                            {/foreach}
                        </table>
                    </div>
                {else}
                    <div class="alert alert-danger">Er is geen Git repo gevonden voor de frontend</div>
                {/if}
            </div>
        </div>
        <div class="card card-secondary">
            <div class="card-header">
                <h2 class="card-title">Database</h2>
                <div class="card-tools">
                    <div class="btn-group">
                        {if $aDBChanges !== [] }
                            <form action="{$smarty.server.REQUEST_URI}" method="post">
                                <input class="btn btn-primary" name="reload" value="Wijzigingen doorvoeren" type="submit" onclick="startPageLoad()">
                            </form>
                        {/if}
                        <button class="btn btn-secondary" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                {if $aDBChanges !== [] }
                        <ul class="list-group">
                            {foreach $aDBChanges as $change}
                                <li class="list-group-item">{$change}</li>
                            {/foreach}
                        </ul>
                    {else}
                        <div class="alert alert-info">
                            Geen database wijzigingen
                        </div>
                {/if}
            </div>
        </div>
    </div>
{*    <div class="row">*}
{*        <div class="col-md-6">*}
{*            <div class="card">*}
{*                <div class="card-header">*}
{*                    <h2 class="card-title">Huidige Versie</h2>*}
{*                    <div class="card-tools">*}
{*                    </div>*}
{*                </div>*}
{*                <div class="card-body table-responsive">*}
{*                    <table class="table">*}
{*                        <tr>*}
{*                            <th>Commit Id</th>*}
{*                            <td>{$LastCommit.commit}</td>*}
{*                        </tr>*}
{*                        <tr>*}
{*                            <th>Onderwerp</th>*}
{*                            <td>{$LastCommit.subject}</td>*}
{*                        </tr>*}
{*                        <tr>*}
{*                            <th>Auteur</th>*}
{*                            <td>{$LastCommit.author}</td>*}
{*                        </tr>*}
{*                        <tr>*}
{*                            <th>Datum</th>*}
{*                            <td>{$LastCommit.date|date_format:"%H:%M %d-%m-%Y"}</td>*}
{*                        </tr>*}

{*                    </table>*}
{*                </div>*}
{*            </div>*}
{*        </div>*}
{*        <div class="col-md-6">*}
{*            <div class="card">*}
{*                <div class="card-header">*}
{*                    <h2 class="card-title">Updaten</h2>*}
{*                    <div class="card-tools">*}
{*                    </div>*}
{*                </div>*}
{*                <div class="card-body">*}
{*                    <form action="{$smarty.server.REQUEST_URI}" method="post">*}
{*                        <input class="btn btn-info" name="gitpull" value="Updaten" type="submit" onclick="startPageLoad()">*}
{*                        <input class="btn btn-primary" name="reload" value="Reload Database Structure" type="submit" onclick="startPageLoad()">*}
{*                        <input class="btn btn-danger" name="gitreset" value="Reset Repo" type="submit" onclick="startPageLoad()">*}
{*                    </form>*}
{*                </div>*}
{*            </div>*}
{*        </div>*}
{*    </div>*}
{*    <div class="card">*}
{*        <div class="card-header">*}
{*            <h2 class="card-title">Status</h2>*}
{*            <div class="card-tools">*}
{*            </div>*}
{*        </div>*}
{*        <div class="card-body table-responsive">*}
{*            <table class="table">*}
{*                {foreach from=$Status item=$s}*}
{*                    {if $s !== ""}*}
{*                        <tr>*}
{*                            <td>{$s}</td>*}
{*                        </tr>*}
{*                    {/if}*}
{*                {/foreach}*}
{*            </table>*}
{*        </div>*}
{*    </div>*}
{/block}