{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/arura/users">Gebruikers</a></li>
    <li class="breadcrumb-item active">Aanmaken</li>
{/block}
{block content}
    <div class="card card-primary">
        <div class="card-body">
            {$form}
        </div>
    </div>
{/block}