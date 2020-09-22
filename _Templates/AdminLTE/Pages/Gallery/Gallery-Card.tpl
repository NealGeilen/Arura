<div class="col-12" data-gallery-id="{$Gallery->getId()}">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-2 col-md-2">
                    <img loading="lazy" src="{$Gallery->getAnCoverImage(false)->getThumbnail(false)}" style="max-width: 100%; height: 100px; display: block; margin: 0 auto">
                </div>
                <div class="col-10 col-md-6">
                    <h4>
                        {if !$Gallery->isPublic()}
                            <i class="fas fa-lock"></i>
                        {/if}
                        {$Gallery->getName()}
                    </h4>
                    <p>{$Gallery->getDescription()}</p>
                    <small>Aangemaakt op {$Gallery->getCreatedDate()|date_format:"%H:%M %d-%m-%Y"}</small>
                </div>
                <div class="col-12 col-md-4">
                    <div class="btn-group float-md-right">
                        <button class="btn btn-primary" onclick="Home.Sortable.updatePublic('{$Gallery->getId()}')">
                            {if $Gallery->isPublic()}
                                <i class="fas fa-lock"></i>
                            {else}
                                <i class="fas fa-lock-open"></i>
                            {/if}
                        </button>
                        <a class="btn btn-secondary" href="/dashboard/gallery/{$Gallery->getId()}">
                            <i class="fas fa-images"></i>
                        </a>
                        <a class="btn btn-secondary" href="/dashboard/gallery/{$Gallery->getId()}/settings">
                            <i class="fas fa-cogs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>