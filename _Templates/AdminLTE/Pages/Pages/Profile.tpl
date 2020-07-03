{extends "../../index.tpl"}
{block content}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <header class="card-header">
                    <h2 class="card-title">Gegevens</h2>
                </header>
                <div class="card-body">
                    {$form->startForm()}
                    <div class="row">
                        <div class="col-md-6">
                            {$form->getControl("User_Username")}
                        </div>
                        <div class="col-md-6">
                            {$form->getControl("User_Email")}
                        </div>
                        <div class="col-md-6">
                            {$form->getControl("User_Firstname")}
                        </div>
                        <div class="col-md-6">
                            {$form->getControl("User_Lastname")}
                        </div>
                    </div>
                    {$form->getControl("submit")}
                    {$form->endForm()}
                </div>
            </div>
        </div>
        <div class="col-md-6">
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
    </div>
    {/block}