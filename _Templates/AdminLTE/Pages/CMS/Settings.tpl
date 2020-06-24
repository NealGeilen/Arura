{extends "../../index.tpl"}
{block content}
    <header class="page-header">
        <h3>{$aCmsPage.Page_Title}</h3>
        <h5>
            <a href="{$aWebsite.url}{$aCmsPage.Page_Url}" target="_blank">{$aWebsite.url}{$aCmsPage.Page_Url}</a>
        </h5>
    </header>

    <section class="card card-primary card-outline">
        <div class="card-header">
            <h2 class="card-title">Basis informatie</h2>
            <div class="card-tools">
                <div class="btn-group btn-group-sm">
                    <a class="btn btn-secondary" href="/{$aArura.dir}/content/paginas">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    {if !$bMobileUser}
                        <a class="btn btn-primary" href="/{$aArura.dir}/content/pagina/{$aCmsPage.Page_Id}/content" target="_blank">
                            <i class="fas fa-pen"></i>
                        </a>
                    {/if}
                </div>
            </div>
        </div>
        <div class="card-body">
            {$form}
        </div>
    </section>
    {/block}