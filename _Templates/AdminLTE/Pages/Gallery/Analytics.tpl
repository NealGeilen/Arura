{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/gallery">Albums</a></li>
    <li class="breadcrumb-item"><a href="/dashboard/gallery/{$Gallery->getId()}">{$Gallery->getName()}</a></li>
    <li class="breadcrumb-item active">Analytics</li>
{/block}

{block content}
    {$Dashboard}
{/block}