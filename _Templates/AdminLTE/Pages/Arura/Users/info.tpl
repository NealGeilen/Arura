{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/arura/users">Gebruikers</a></li>
    <li class="breadcrumb-item active">{$User->getFirstname()}</li>
{/block}
{block content}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Gegevens</h3>
                </div>
                <div class="card-body">
                    {$editForm}
                </div>
            </div>
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Wachtwoord</h3>
                </div>
                <div class="card-body">
                    {$passwordForm}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Rollen</h3>
                </div>
                <div class="card-body">
                    {$roleForm}
                </div>
            </div>
        </div>
    </div>
{/block}