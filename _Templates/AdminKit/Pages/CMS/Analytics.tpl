{extends "../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/content/paginas">Pagina's</a></li>
    <li class="breadcrumb-item active">Analytics: {$CmsPage->getTitle()}</li>
{/block}

{block content}
    {$Dashboard}
{/block}