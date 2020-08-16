<section>
    <h1>{$Gallery->getName()}</h1>
    <p>{$Gallery->getDescription()}</p>
    <div class="row Gallery">
        {foreach $Gallery->getImages() as $Image}
            <div class="col-md-3 col-sm-6 col-12">
                <div class="mb-3">
                    <a href="{$Image->getImage(false)}">
                        <img class="lozad" data-src="{$Image->getThumbnail(false)}" alt="{$Image->getName()}">
                    </a>
                </div>
            </div>
        {/foreach}
    </div>
</section>