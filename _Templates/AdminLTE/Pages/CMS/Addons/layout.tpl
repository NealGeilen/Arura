{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/content/addons">Addons</a></li>
    <li class="breadcrumb-item active">{$Addon.Addon_Name} Indeling</li>
{/block}

{block content}
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#fields-tabe" role="tab">Velden</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#assets-tabe" role="tab">Assets</a>
        </li>
        <li class="nav-item" id="php-tab">
            <a class="nav-link" data-toggle="tab" href="#php-tabe" role="tab">Php editor</a>
        </li>
        <li class="nav-item" id="html-tab">
            <a class="nav-link" data-toggle="tab" href="#html-tabe" role="tab">Html editor</a>
        </li>

    </ul>


    <div class="tab-content">
        <div class="tab-pane fade show active" id="fields-tabe" role="tabpanel" aria-labelledby="home-tab">
            <div class="card bg-primary">
                <div class="card-body">
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="assets-tabe" role="tabpanel" aria-labelledby="home-tab">
            <div class="card bg-secondary">
                <div class="card-body">
                    <button class="btn btn-primary mb-2">
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="row">
                        <div class="col-12">
                            <div class="rounded w-100 p-2 mb-2 bg-primary">
                                <div class="row">
                                    <div class="col-4">
                                        [NAME]
                                    </div>
                                    <div class="col-4">
                                        [TYPE] CDN or local
                                    </div>
                                    <div class="col-4">
                                        <div class="btn-group border border-white rounded float-right">
                                            <button class="btn btn-primary">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="rounded w-100 p-2 mb-2 bg-primary">
                                <div class="row">
                                    <div class="col-4">
                                        [NAME]
                                    </div>
                                    <div class="col-4">
                                        [TYPE] CDN or local
                                    </div>
                                    <div class="col-4">
                                        <div class="btn-group border border-white rounded float-right">
                                            <button class="btn btn-primary">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="rounded w-100 p-2 mb-2 bg-primary">
                                <div class="row">
                                    <div class="col-4">
                                        [NAME]
                                    </div>
                                    <div class="col-4">
                                        [TYPE] CDN or local
                                    </div>
                                    <div class="col-4">
                                        <div class="btn-group border border-white rounded float-right">
                                            <button class="btn btn-primary">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="php-tabe" role="tabpanel" aria-labelledby="home-tab">
            <div class="card bg-secondary">
                <div class="card-body">
                    <textarea id="php-editor">
    public static function getAllAddons(bool $NeedsActive = true){
        $db = new Database();
        $aAddons = $db->fetchAll('SELECT * FROM tblCmsAddons'.($NeedsActive ? ' WHERE Addon_Active = 1' : null));
        $aList = [];
        foreach ($aAddons as $ikey =>$aAddon){
            $aList[(int)$aAddon['Addon_Id']] = $aAddon;
            $aList[(int)$aAddon['Addon_Id']]['AddonSettings'] = $db->fetchAll('SELECT * FROM tblCmsAddonSettings WHERE AddonSetting_Addon_Id = ? ORDER BY AddonSetting_Position ASC',
                [
                    (int)$aAddon['Addon_Id']
                ]);
        }
        return$aList;
    }
                    </textarea>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="html-tabe" role="tabpanel" aria-labelledby="home-tab">
            <div class="card bg-secondary">
                <div class="card-body">
                    <textarea id="html-editor">
                        {literal}
                            {$form->startForm()}
                            {if $form->isSuccess()}
                            <div class="alert alert-success bg-success text-white border-0">
                            <h1>Verzonden</h1>
                            </div>
                            {/if}
                            <div class="row">
                            <div class="col-6">
                            {$form->getControl("FirstName")}
                            </div>
                            <div class="col-6">
                            {$form->getControl("LastName")}
                            </div>
                            <div class="col-12">
                            {$form->getControl("Date")}
                            </div>
                            </div>
                            {$form->getControl("submit")}
                            {$form->endForm()}
                        {/literal}
                    </textarea>
                </div>
            </div>
        </div>
    </div>
{/block}