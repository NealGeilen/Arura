{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Profiel: {$aUser.User_Username}</li>
{/block}

{block content}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <header class="card-header">
                    <h2 class="card-title">Gegevens</h2>
                </header>
                <div class="card-body">
                    {$form}
                </div>
            </div>
            <div class="card card-secondary">
                <header class="card-header">
                    <h2 class="card-title">Wachtwoord</h2>
                </header>
                <div class="card-body">
                    {$PasswordForm->startForm()}
                    <div class="row">
                        <div class="col-6">
                            {$PasswordForm->getControl("password_1")}
                        </div>
                        <div class="col-6">
                            {$PasswordForm->getControl("password_2")}
                        </div>
                    </div>
                    {$PasswordForm->getControl("submit")}
                    {$PasswordForm->endForm()}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-secondary">
                <header class="card-header">
                    <h2 class="card-title">Rechten</h2>
                </header>
                <div class="card-body">
                    <ul class="list-group">
                    {foreach $roles as $role}
                        <li class="list-group-item"><span class="text-success"><i class="fas fa-check"></i></span> {$allRoles[$role].Name}</li>
                        {foreachelse}
                        <li class="list-group-item"><span class="text-success">Op het moment heb je geen rechte op deze omgeving</li>
                    {/foreach}
                    </ul>
                </div>
            </div>
        </div>
    </div>
    {/block}