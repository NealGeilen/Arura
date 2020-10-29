{extends "../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/redirects/shorten">Url verkleinen</a></li>
    <li class="breadcrumb-item active">{$Url->getToken()}</li>
{/block}


{block content}
    {$Dashboard}
{/block}