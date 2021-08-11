{extends "../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item active">Gebruikers</li>
{/block}
{block pageactions}
    <a class="btn btn-primary" href="/dashboard/arura/users/create">
        <i class="fas fa-plus"></i>
    </a>
{/block}
{block content}
    <div class="card card-primary">
        <header class="card-header">
            <h2 class="card-title">Gebruikers</h2>
        </header>
        <div class="card-body table-responsive">
            <table class="table Arura-Table">
                <thead>
                <tr>
                    <th>
                        Gebruikersnaam
                    </th>
                    <th>
                        Voornaam
                    </th>
                    <th>
                        Achternaam
                    </th>
                    <th>
                        Email
                    </th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {foreach $aUsers as $aUser}
                    <tr>
                        <td>{$aUser.User_Username}</td>
                        <td>{$aUser.User_Firstname}</td>
                        <td>{$aUser.User_Lastname}</td>
                        <td>{$aUser.User_Email}</td>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-primary" href="/dashboard/arura/user/{$aUser.User_Id}"><i class="fas fa-pen"></i></a>
                            </div>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>

    <div class="card card-secondary">
        <header class="card-header">
            <h2 class="card-title">Sessies</h2>
        </header>
        <div class="card-body table-responsive">
            <table class="table Arura-Table">
                <thead>
                <tr>
                    <th>
                        Voornaam
                    </th>
                    <th>
                        Achternaam
                    </th>
                    <th>
                        Tijd laatste actie
                    </th>
                </tr>
                </thead>
                <tbody>
                {foreach $aSessions as $aSession}
                    <tr>
                        <td>{$aSession.User_Firstname}</td>
                        <td>{$aSession.User_Lastname}</td>
                        <td>{$aSession.Session_Last_Active|date_format:"%H:%M %d-%m-%Y"}</td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/block}