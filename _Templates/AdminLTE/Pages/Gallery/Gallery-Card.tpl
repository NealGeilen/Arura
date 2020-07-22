<div class="col-md-4 col-6" data-gallery-id="{$Gallery->getId()}">
    <div class="gallery-container border" style="background-image: url('{$Gallery->getAnCoverImage(false)->getThumbnail(false)}')">
        {if !$Gallery->isPublic()}
            <div class="lock">
                <i class="fas fa-lock"></i>
            </div>
        {/if}
        <h4 class="gallery-name">
            {$Gallery->getName()}
        </h4>
        <div class="gallery-options">
            <span class="btn btn-gallery-toolbar mover handle btn-lg"><i class="fas fa-arrows-alt"></i></span>
            <div class="gallery-toolbar btn-group">
                <button class="btn btn-gallery-toolbar" onclick="Home.Sortable.updatePublic('{$Gallery->getId()}')">
                    {if $Gallery->isPublic()}
                        <i class="fas fa-lock"></i>
                    {else}
                        <i class="fas fa-lock-open"></i>
                    {/if}
                </button>
                <a class="btn btn-gallery-toolbar" href="/dashboard/gallery/{$Gallery->getId()}">
                    <i class="fas fa-pen"></i>
                </a>
            </div>
        </div>
    </div>
</div>