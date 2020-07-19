{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/gallery">Albums</a></li>
    <li class="breadcrumb-item active">{$Gallery->getName()}</li>
{/block}

{block contentHeader}
    <button class="btn btn-primary" data-toggle="modal" data-target="#">Afbeelding uploaden</button>
    <a class="btn btn-secondary" href="/dashboard/gallery/{$Gallery->getId()}/settings">Instellingen</a>
{/block}

{block content}
    {foreach $Gallery->getImages(false) as $Image}
        {$Image|var_dump}
    {/foreach}

{/block}