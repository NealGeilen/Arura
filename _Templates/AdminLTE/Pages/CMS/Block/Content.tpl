{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/content/pagina/{$CmsPage->getId()}/content">Pagina: {$CmsPage->getTitle()}</a></li>
    <li class="breadcrumb-item active">Content block</li>
{/block}

{block content}
    <script>
        var _Content = {$Block->getValue()|json_encode};
        var _Fields = {$Addon->getFields()|json_encode};
        var _Raster = {$Block->GetRaster()};
        var _IsMultiple = {$Addon->isMultipleValues()};
    </script>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-body bg-primary">
                <h2>Naam: {$Addon->getName()}</h2>
                <h4>Soort: {$Addon->getType()|ucfirst}</h4>
            </div>
        </div>
        {if $Addon->getType() != "custom" && $Addon->isMultipleValues() && !$Addon->isListStyle()}
        <div class="col-md-6">
            <div class="card card-body bg-primary">
                <h5>Raster groote</h5>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-secondary {if $Block->GetRaster() === 12}active{/if}" onclick="Builder.Item.ChangeRaster(12, this)">
                        <input type="radio"> 12
                    </label>
                    <label class="btn btn-secondary {if $Block->GetRaster() === 6}active{/if}" onclick="Builder.Item.ChangeRaster(6, this)">
                        <input type="radio"> 6-6
                    </label>
                    <label class="btn btn-secondary {if $Block->GetRaster() === 4}active{/if}" onclick="Builder.Item.ChangeRaster(4, this)">
                        <input type="radio"> 4-4-4
                    </label>
                    <label class="btn btn-secondary {if $Block->GetRaster() === 3}active{/if}" onclick="Builder.Item.ChangeRaster(3,this)">
                        <input type="radio" > 3-3-3-3
                    </label>
                </div>
            </div>
        </div>
        {/if}
    </div>
    {if $Addon->getType() != "custom"}
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Content</h3>
                <div class="card-tools">
                    {if $Addon->isMultipleValues()}
                    <button class="btn btn-primary" onclick="Builder.Item.Create()">
                        <i class="fas fa-plus"></i>
                    </button>
                    {/if}
                    <button class="btn btn-primary" onclick="Builder.Structure.save()">
                        <i class="fas fa-save"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="Groups row">
                </div>
            </div>
        </div>
        {else}
        <div class="alert alert-info">
            Een custom addon kan niet bewerkt worden!
        </div>
    {/if}

    <div class="d-none">
        <div class="template-group">
            <div class="Group-Item col-md-12 ">
                {if $Addon->isMultipleValues()}
                <div class="btn-group-vertical">
                            <span class="btn btn-primary Group-handle">
                                <i class="fas fa-arrows-alt"></i>
                            </span>
                    <button class="btn btn-primary" onclick="Builder.Item.Delete($(this).parents('.Group-Item'))">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                {/if}
                <div class="content bg-secondary">

                </div>
            </div>
        </div>
    </div>

{/block}