{extends "../../index.tpl"}
{block content}
    <div class="row">
        {foreach $aGalleries as $aGallery}
            <div class="col-md-3">
                <a href="/dashboard/gallery/{$aGallery->getId()}">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h4 class="card-title text-capitalize">{$aGallery->getName()}</h4>
                        </div>
                        <div class="card-body">
                        </div>
                    </div>
                </a>
            </div>
        {/foreach}
    </div>
{/block}