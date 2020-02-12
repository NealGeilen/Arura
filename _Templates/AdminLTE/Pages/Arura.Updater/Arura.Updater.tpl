{extends "$TEMPLATEDIR/index.tpl"}
{block content}
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Huidige Versie</h2>
                    <div class="card-tools">
                    </div>
                </div>
                <div class="card-body table-responsive">
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
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Updaten</h2>
                    <div class="card-tools">
                    </div>
                </div>
                <div class="card-body">
                    <form action="{$smarty.server.REQUEST_URI}" method="post">
                        <input class="btn btn-info" name="gitpull" value="Updaten" type="submit" onclick="startPageLoad()">
                        <input class="btn btn-primary" name="reload" value="Reload Database Structure" type="submit" onclick="startPageLoad()">
                        <input class="btn btn-danger" name="gitreset" value="Reset Repo" type="submit" onclick="startPageLoad()">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Status</h2>
            <div class="card-tools">
            </div>
        </div>
        <div class="card-body table-responsive">
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
    </div>
{/block}