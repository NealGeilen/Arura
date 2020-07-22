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
    <div class="row galleries">
        {foreach $aGalleries as $Gallery}
            {include file='./Gallery-Card.tpl'}
            {foreachelse}
            <div class="col-12 image-alert">
                <div class="alert alert-info bg-info">
                    <h5>Geen album's aanwezig</h5>
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
        Home.init();
        {if $createForm->hasErrors()}
        $("#createGallery").modal("show");
        {/if}
    </script>
{/block}