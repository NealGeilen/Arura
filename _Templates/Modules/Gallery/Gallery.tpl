<section>
    <h1>{$Gallery->getName()}</h1>
    <p>{$Gallery->getDescription()}</p>
    <div class="row Gallery">
        {foreach $Gallery->getImages() as $Image}
            <div class="col-md-3 col-sm-6 col-12">
                <div class="mb-3">
                    <a href="{$Image->getImage(false)}">
                        <img src="{$Image->getThumbnail(false)}">
                    </a>
                </div>
            </div>
        {/foreach}
    </div>
</section>