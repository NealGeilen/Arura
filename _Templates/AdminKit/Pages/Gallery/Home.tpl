{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Albums</li>
{/block}

{block content}
    <div class="row">
        <div class="col-md-4">
            <button class="btn btn-primary" data-toggle="modal" data-target="#createGallery">Nieuw album</button>
        </div>
        <div class="col-md-4">
            <form>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Zoeken" name="q" value="{if isset($smarty.get.q)}{$smarty.get.q}{/if}">
                    <input type="hidden" name="p" value="1">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row galleries">
        {foreach $aGalleries as $Gallery}
            {include file='./Gallery-Card.tpl'}
            {foreachelse}
            <div class="col-12 image-alert">
                <div class="alert alert-info bg-info">
                    <h5>Geen album's gevonden</h5>
                </div>
            </div>
        {/foreach}
    </div>
    {include file='./../../pagination.tpl'}

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