{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/gallery">Albums</a></li>
    <li class="breadcrumb-item active">{$Gallery->getName()}</li>
{/block}

{block contentHeader}
    <button class="btn btn-primary" data-toggle="modal" data-target="#uploadImage">Afbeelding uploaden</button>
    <a class="btn btn-secondary" href="/dashboard/gallery/{$Gallery->getId()}/settings">Instellingen</a>
{/block}

{block content}
    {foreach $Gallery->getImages(false) as $Image}
        {include file='./Image.tpl'}
    {/foreach}

    <!-- Modal -->
    <div class="modal fade" id="uploadImage">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="dropzone" id="dp-UploadImage"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
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