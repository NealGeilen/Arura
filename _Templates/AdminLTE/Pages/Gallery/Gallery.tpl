{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/gallery">Albums</a></li>
    <li class="breadcrumb-item active">{$Gallery->getName()}</li>
{/block}

{block contentHeader}
    <button class="btn btn-primary" data-toggle="modal" data-target="#uploadImage"><i class="fas fa-upload"></i></button>
    <a class="btn btn-secondary" href="/dashboard/gallery/{$Gallery->getId()}/settings"><i class="fas fa-cogs"></i></a>
    <a href="{$aWebsite.url}/album/{$Gallery->getId()}" target="_blank" class="btn btn-secondary"><i class="fas fa-eye"></i></a>
{/block}

{block content}
    <div class="row images">
        {foreach $Gallery->getImages(false) as $Image}
            {include file='./Image-card.tpl'}
            {foreachelse}
            <div class="col-12 image-alert">
                <div class="alert alert-info bg-info">
                    <h5>Geen afbeeldingen in album</h5>
                </div>
            </div>
        {/foreach}
    </div>

    <!-- Modal -->
    <div class="modal fade" id="uploadImage">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Afbeeldingen uploaden</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="dropzone" id="dp-UploadImage"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    <button type="button" class="btn btn-primary upload-images">Toevoegen</button>
                </div>
            </div>
        </div>
    </div>

{/block}


{block JsPage}
    <script>
        Gallery.init();
    </script>
{/block}