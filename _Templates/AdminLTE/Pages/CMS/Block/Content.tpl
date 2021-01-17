{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/content/pagina/{$CmsPage->getId()}/content">Pagina: {$CmsPage->getTitle()}</a></li>
    <li class="breadcrumb-item active">Content block</li>
{/block}

{block content}


    <div class="row">
        <div class="col-md-6">
            <div class="card card-body bg-primary">
                <h2>Naam: {$Addon->getName()}</h2>
                <h4>Soort: {$Addon->getType()|ucfirst}</h4>
            </div>
        </div>
        {if $Addon->getType() != "custom"}
        <div class="col-md-6">
            <div class="card card-body bg-primary">
                <h5>Raster groote</h5>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-secondary active">
                        <input type="radio" name="options" id="option1" autocomplete="off" checked> 12
                    </label>
                    <label class="btn btn-secondary">
                        <input type="radio" name="options" id="option2" autocomplete="off"> 6-6
                    </label>
                    <label class="btn btn-secondary">
                        <input type="radio" name="options" id="option3" autocomplete="off"> 4-4-4
                    </label>
                    <label class="btn btn-secondary">
                        <input type="radio" name="options" id="option3" autocomplete="off"> 3-3-3-3
                    </label>
                </div>
            </div>
        </div>
        {/if}
    </div>
    {if $Addon->getType() != "custom"}
        <div class="card bg-secondary">
            <div class="card-header">
                <h3 class="card-title">Content</h3>
                <div class="card-tools">
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="Group row">
                    <div class="Group-Item active position-relative col-md-6">
                        <div class="btn-group-vertical">
                            <span class="btn btn-primary">
                                <i class="fas fa-arrows-alt-v"></i>
                            </span>
                            <button class="btn btn-primary">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="row">
                            {foreach $Addon->getFields() as $Field}
                                <div class="col-12">
                                    {$Field|var_dump}
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {else}
        <div class="alert alert-info">
            Een custom addon kan niet bewerkt worden!
        </div>
    {/if}
{/block}