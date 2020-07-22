

<div class="col-md-3 col-4" data-image-id="{$Image->getId()}">
    <div class="image-container border" style="background-image: url('{$Image->getThumbnail(false)}')">
        {if !$Image->isPublic()}
            <div class="lock">
                <i class="fas fa-lock"></i>
            </div>
        {/if}
        {if $Image->isCover()}
            <div class="cover" title="Cover afbeeldingen">
                <i class="fas fa-image"></i>
            </div>
        {/if}
        <div class="image-options">
        <span class="btn btn-image-toolbar mover handle btn-lg">
            <i class="fas fa-arrows-alt"></i>
        </span>
            <div class="image-toolbar btn-group">
                <button class="btn btn-image-toolbar" onclick="Gallery.Sortable.updatePublic('{$Image->getId()}')">
                    {if $Image->isPublic()}
                        <i class="fas fa-lock"></i>
                    {else}
                        <i class="fas fa-lock-open"></i>
                    {/if}
                </button>
                <a class="btn btn-image-toolbar" href="/dashboard/gallery/image/{$Image->getId()}">
                    <i class="fas fa-pen"></i>
                </a>
                <button class="btn btn-image-toolbar" onclick="Gallery.Sortable.updateCover('{$Image->getId()}')">
                    {if $Image->isCover()}
                        <i class="far fa-images"></i>
                    {else}
                        <i class="far fa-image"></i>
                    {/if}
                </button>
                <a class="btn btn-image-toolbar" href="/gallery/image/{$Image->getId()}/download" target="_blank">
                    <i class="fas fa-download"></i>
                </a>
            </div>
        </div>
    </div>
</div>