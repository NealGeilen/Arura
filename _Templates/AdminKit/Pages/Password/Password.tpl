{extends "../../security.tpl"}
{block content}
    <div class="container">
        <img src="{$aWebsite.logo}" style="max-width: 100%; max-height: 100px; margin: 0 auto; display: block">
        <h1 class="text-center w-100">{$aWebsite.name}</h1>
        <div class="card">
            <div class="card-body">
                <p>Je hebt bijna een nieuw wachtwoord nog een paar stappen.</p>

                {$form}
            </div>
        </div>
    </div>
{/block}