{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Albums</li>
{/block}

{block contentHeader}
    <div class="btn-group">
        <button class="btn btn-primary" data-toggle="modal" data-target="#createGallery">Nieuw album</button>
    </div>
{/block}

{block content}
    <div class="row">
        {foreach $aGalleries as $Gallery}
            <div class="col-md-4 col-6">

                <div class="gallery-container" style="background-image: url('{$Gallery->getAnCoverImage(false)->getThumbnail(false)}')">
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
                            <button class="btn btn-gallery-toolbar" >
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
        {/foreach}
    </div>

    <!-- Modal -->
    <div class="modal fade" id="createGallery"  role="dialog" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {$createForm->startForm()}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Album aanmaken</h5>
                </div>
                <div class="modal-body">
                    {$createForm->getControl("Gallery_Name")}
                    {$createForm->getControl("Gallery_Slug")}
                    {$createForm->getControl("Gallery_Public")}
                    {$createForm->getControl("Gallery_Description")}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    {$createForm->getControl("submit")}
                </div>
                {$createForm->endForm()}
            </div>
        </div>
    </div>
{/block}

{block JsPage}
    <script>
        {if $createForm->hasErrors()}
        $("#createGallery").modal("show");
        {/if}
    </script>
{/block}