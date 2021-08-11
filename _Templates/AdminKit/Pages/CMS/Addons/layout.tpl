{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/content/addons">Addons</a></li>
    <li class="breadcrumb-item active">{$Addon->getName()} Indeling</li>
{/block}

{block content}
    <ul class="nav nav-tabs" role="tablist">
        {if $Addon->isWidget()}
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#fields-tabe" role="tab">Velden</a>
            </li>
        {/if}
        <li class="nav-item">
            <a class="nav-link {if !$Addon->isWidget()}active{/if}" data-toggle="tab" href="#assets-tabe" role="tab">Assets</a>
        </li>
            <li class="nav-item" id="php-tab">
                <a class="nav-link" data-toggle="tab" href="#php-tabe" role="tab">Php editor</a>
            </li>
        {if $Addon->hasTemplateFile()}
            <li class="nav-item" id="html-tab">
                <a class="nav-link" data-toggle="tab" href="#html-tabe" role="tab">Html editor</a>
            </li>
        {/if}

    </ul>


    <div class="tab-content">
        {if $Addon->isWidget()}
        <div class="tab-pane fade show active" id="fields-tabe" role="tabpanel" aria-labelledby="home-tab">
            <div class="card bg-primary">
                <div class="card-body">
                    <button class="btn btn-secondary mb-2" data-toggle="modal" data-target="#FieldAddForm">
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="fields">
                        {foreach $Addon->getFields() as $index => $Field}
                            <div class="rounded w-100 p-2 mb-2 bg-secondary" field-id="{$Field.AddonSetting_Id}">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="btn btn-primary handle">
                                            <i class="fas fa-arrows-alt"></i>
                                        </div>
                                        {$Field.AddonSetting_Tag}
                                        {*                                    {$Asset.fileType|upper}*}
                                    </div>
                                    <div class="col-3">
                                        {$Field.AddonSetting_Type}
                                        {*                                    {$Asset.type|upper}*}
                                    </div>
                                    <div class="col-4">
                                        {*                                    {$Asset.src}*}
                                    </div>
                                    <div class="col-2">
                                        <button class="btn btn-primary edit float-right" type="button" data-toggle="collapse" data-target="#edit-bar-{$index}" aria-expanded="false">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="collapse" id="edit-bar-{$index}">
                                    <div class="rounded p-3 bg-white mt-2">
                                        {$Addon->EditFieldForm($Field.AddonSetting_Id)}
                                        {$Addon->RemoveFieldForm($Field.AddonSetting_Id)}
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    </div>
{*                    {$Addon->getFields()|var_dump}*}
                </div>
            </div>
        </div>
        {/if}
        <div class="tab-pane fade{if !$Addon->isWidget()} show active{/if}" id="assets-tabe" role="tabpanel" aria-labelledby="home-tab">
            <div class="card bg-secondary">
                <div class="card-body">
                    <button class="btn btn-primary mb-2" data-toggle="modal" data-target="#AssetAddForm">
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="row">
                        {foreach $Addon->getAssets() as $index => $Asset}
                            <div class="col-12">
                                <div class="rounded w-100 p-2 mb-2 bg-primary">
                                    <div class="row">
                                        <div class="col-3">
                                            {$Asset.fileType|upper}
                                        </div>
                                        <div class="col-3">
                                            {$Asset.type|upper}
                                        </div>
                                        <div class="col-4">
                                            {$Asset.src}
                                        </div>
                                        <div class="col-2">
                                            <div class="btn-group float-right">
                                                <button class="btn btn-primary edit" type="button" data-toggle="collapse" data-target="#edit-bar-{$index}" aria-expanded="false">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="collapse" id="edit-bar-{$index}">
                                        <div class="rounded p-3 bg-white mt-2">
                                            {$Addon->EditAssetForm($index)}
                                            {$Addon->RemoveAssetForm($index)}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                            </div>
                            {foreachelse}
                            <div class="col-12">
                                <div class="alert alert-info">
                                    Geen assets toegevoegd
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
            <div class="tab-pane fade" id="php-tabe" role="tabpanel" aria-labelledby="home-tab">
                <div class="card bg-secondary">
                    <div class="card-body">
                        {if !$Addon->hasPhpFile()}
                            <div class="alert alert-info">
                                Nog geen php bestand beschikbaar, Sla op om een te maken
                            </div>
                        {/if}
                        {$Addon->getPhpForm()}
                    </div>
                </div>
            </div>
        {if $Addon->hasTemplateFile()}
            <div class="tab-pane fade" id="html-tabe" role="tabpanel" aria-labelledby="home-tab">
                <div class="card bg-secondary">
                    <div class="card-body">
                        {$Addon->getTemplateForm()}
                    </div>
                </div>
            </div>
        {/if}
    </div>

    <div class="modal fade" id="AssetAddForm"  role="dialog" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {$AssetAddForm->startForm()}
                <div class="modal-header">
                    <h5 class="modal-title">Asset toevoegen</h5>
                </div>
                <div class="modal-body">
                    {$AssetAddForm->getControl("src")}
                    {$AssetAddForm->getControl("type")}
                    {$AssetAddForm->getControl("file")}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    {$AssetAddForm->getControl("submit")}
                </div>
                {$AssetAddForm->endForm()}
            </div>
        </div>
    </div>

    <div class="modal fade" id="FieldAddForm"  role="dialog" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {$FieldAddForm->startForm()}
                <div class="modal-header">
                    <h5 class="modal-title">veld toevoegen</h5>
                </div>
                <div class="modal-body">
                    {$FieldAddForm->getControl("tag")}
                    {$FieldAddForm->getControl("type")}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    {$FieldAddForm->getControl("submit")}
                </div>
                {$FieldAddForm->endForm()}
            </div>
        </div>
    </div>
{/block}


{block JsPage}
    <script>
        {if $AssetAddForm->hasErrors()}
        $("#AssetAddForm").modal("show");
        {/if}
        {if $FieldAddForm->hasErrors()}
        $("#FieldAddForm").modal("show");
        {/if}
    </script>
{/block}