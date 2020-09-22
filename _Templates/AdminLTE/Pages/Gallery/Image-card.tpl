<div class="col-12" data-image-id="{$Image->getId()}">
    <div class="card">
        <div class="card-body">
            <div class="btn btn-outline-primary mover handle">
                <div class="item">
                    <i class="fas fa-arrows-alt-v"></i>
                </div>
            </div>
            <div class="row">
                <div class="col-2 col-md-2">
                    <a href="{$Image->getImage(false)}" target="_blank">
                        <img loading="lazy" alt="{$Image->getName()}" src="{$Image->getThumbnail(false)}" style="max-width: 100%; height: 100px; display: block; margin: 0 auto">
                    </a>
                </div>
                <div class="col-10 col-md-6">
                    <h4>
                        {if !$Image->isPublic()}
                            <i class="fas fa-lock"></i>
                        {/if}
                        {if $Image->isCover()}
                            <i class="fas fa-image"></i>
                        {/if}
                        {$Image->getName()}
                    </h4>
                </div>
                <div class="col-12 col-md-4">
                    <div class="btn-group float-md-right">
                        <button class="btn btn-primary" onclick="Gallery.Sortable.updatePublic('{$Image->getId()}')">
                            {if $Image->isPublic()}
                                <i class="fas fa-lock"></i>
                            {else}
                                <i class="fas fa-lock-open"></i>
                            {/if}
                        </button>
                        <a class="btn btn-secondary" href="/dashboard/image/{$Image->getId()}">
                            <i class="fas fa-pen"></i>
                        </a>
                        <button class="btn btn-secondary" onclick="Gallery.Sortable.updateCover('{$Image->getId()}')">
                            {if $Image->isCover()}
                                <i class="far fa-images"></i>
                            {else}
                                <i class="far fa-image"></i>
                            {/if}
                        </button>
                        <a class="btn btn-secondary" href="/gallery/image/{$Image->getId()}/download" target="_blank">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>