{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/gallery">Albums</a></li>
    <li class="breadcrumb-item"><a href="/dashboard/gallery/{$Image->getGallery()->getId()}">{$Image->getGallery()->getName()}</a></li>
    <li class="breadcrumb-item active">{$Image->getName()}</li>
{/block}


{block content}
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h2 class="card-title">Instellingen</h2>
            </div>
            <div class="card-body">
                {$Image->getForm()}
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-danger">
            <div class="card-header">
                <h2 class="card-title">Verwijderen</h2>
            </div>
            <div class="card-body">
                {$Image->getDeleteForm()}
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card card-secondary">
            <div class="card-header">
                <h2 class="card-title">Gegevens</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Thumbnail</h4>
                        <img src="{$Image->getThumbnail(false)}" style="max-width: 100%">
                    </div>
                    <div class="col-md-6">
                        <h4>Afbeelding</h4>
                        <img src="{$Image->getImage(false)}" style="max-width: 100%">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
