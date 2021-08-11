{extends "../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/content/paginas">Pagina's</a></li>
    <li class="breadcrumb-item active">Instellingen: {$aCmsPage.Page_Title}</li>
{/block}

{block pageactions}
    <a class="btn btn-primary" href="/{$aArura.dir}/content/paginas">
        <i class="fas fa-arrow-left"></i>
    </a>
    <a href="/dashboard/content/pagina/{$aCmsPage.Page_Id}/analytics" class="btn btn-secondary">
        <i class="fas fa-chart-line"></i>
    </a>
    {if !$bMobileUser}
        <a class="btn btn-secondary" href="/{$aArura.dir}/content/pagina/{$aCmsPage.Page_Id}/content">
            <i class="fas fa-pen"></i>
        </a>
    {/if}
{/block}

{block content}
    <div class="card card-primary">
        <div class="card-header">
            <div class="card-title">
                <h3>{$aCmsPage.Page_Title}</h3>
                <h5>
                    <a href="{$aWebsite.url}{$aCmsPage.Page_Url}" target="_blank">{$aWebsite.url}{$aCmsPage.Page_Url}</a>
                </h5>
            </div>
        </div>
        <div class="card-body">
            {$form}
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            {$CmsPage->getDeleteForm()}
        </div>
    </div>
{/block}