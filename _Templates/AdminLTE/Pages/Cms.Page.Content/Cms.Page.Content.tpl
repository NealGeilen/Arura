{extends "$TEMPLATEDIR/index.tpl"}
{block content}
    <script>
        var _Page_Id = {$aCmsPage.Page_Id}
    </script>

    <header class="page-header">
        <h3>{$aCmsPage.Page_Title}</h3>
        <h5>
            <a href="{$aWebsite.url}{$aCmsPage.Page_Url}" target="_blank">{$aWebsite.url}{$aCmsPage.Page_Url}</a>
        </h5>
    </header>

    <div class="CMS-overvieuw">
        <div class="card">
            <header class="card-header page-toolbar">
                <div class="btn-group btn-group-sm" style="float: right">
                    <a class="btn btn-default" href="/{$aArura.dir}/content/pagina/instellingen?p={$aCmsPage.Page_Id}" target="_blank">
                        <i class="fas fa-cog"></i>
                    </a>
                    <button class="btn btn-primary" onclick="Builder.Structure.save(true)">
                        <i class="fas fa-save"></i>
                    </button>
                </div>
            </header>
            <div class="card-body">
                <div class="CMS-Page-Editor">

                </div>
                <div class="group-add">
                    <div class="content">
                        <h3>Groep toevoegen.</h3>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-secondary" onclick="Builder.Group.Add()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="block-editor-background">
        <div class="btn-group-vertical">
            <div class="btn btn-primary" onclick="Sidebar.Block.Edit.End()">
                <i class="fas fa-times"></i>
            </div>
            <div class="btn btn-primary" onclick="Sidebar.Block.Edit.Add()">
                <i class="fas fa-plus"></i>
            </div>
        </div>
    </div>


    <div style="display: none">
        <div class="template-page-group">
            <div class="CMS-Group">
                <div class="btn-group btn-group-sm CMS-Group-Control ">
                    <button class="btn btn-sm btn-secondary" onclick="Builder.Block.Create($(this).parents('.CMS-Group'))">
                        <i class="fas fa-plus"></i>
                    </button>
                    <span class="btn btn-sm btn-secondary Group-Position-Handler">
                    <i class="fas fa-arrows-alt-v"></i>
                </span>
                    <button class="btn btn-sm btn-secondary" onclick="Builder.Group.Delete($(this).parents('.CMS-Group'))">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                <div class="CMS-Group-Content row">
                </div>
            </div>
        </div>
        <div class="template-edit-item">
            <div class="Block-Editor">
                <div class="btn-group">
                <span class="btn btn-default Block-Editor-Handle">
                    <i class="fas fa-arrows-alt"></i>
                </span>
                    <button class="btn btn-default" onclick="Sidebar.Block.Edit.Remove($(this))">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="template-page-block">
            <div class="Block-Item">
                <div class="Block-Item-Control">
                    <div class="btn-group-vertical btn-group-sm">
                    <span class="btn btn-xsm btn-primary Block-Item-Position-Handle">
                        <i class="fas fa-arrows-alt"></i>
                    </span>
                        <button class="btn btn-xsm btn-primary" onclick="Builder.Block.Delete($(this).parents('.Block-Item'))">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="Block-Item-Content">
                </div>
                <span class="btn btn-sm btn-primary Block-Item-Width-Control ui-resizable-handle ui-resizable-e">
                        <i class="fas fa-arrows-alt-h"></i>
            </span>
            </div>
        </div>
    </div>


    <div class="template-add-block-modal" style="display: none">
        <div class="row">
            <div class="col-12">
                <h4>Standaard</h4>
                <div class="btn-group-toggle row" data-toggle="buttons" addon-types="standard">
                </div>
            </div>
            <div class="col-12">
                <h4>Widgets</h4>
                <div class="btn-group-toggle row" addon-types="widget" data-toggle="buttons">
                </div>
            </div>
            <div class="col-12">
                <h4>Custom</h4>
                <div class="btn-group-toggle row" addon-types="custom" data-toggle="buttons">
                </div>
            </div>
        </div>
    </div>
{/block}
{block sidebar}
    <aside class="control-sidebar control-sidebar-dark">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#block-tab" data-toggle="tab" class="text-center" aria-expanded="true"><i class="fas fa-square"></i> Block</a>
            </li>
            <li class="">
                <a href="#group-tab" data-toggle="tab" class="text-center" aria-expanded="false"><i class="far fa-object-group"></i> Groep</a>
            </li>
        </ul>
        <div class="tab-content">
            <div id="group-tab" class="tab-pane">
                <div class="group-settings" style="display: none">
                    <h3>Groep</h3>
                    <div class="form-group">
                        <label>Css class</label>
                        <input type="text" class="form-control group-settings-field" field="Group_Css_Class">
                    </div>
                    <div class="form-group">
                        <label>Css id</label>
                        <input type="text" class="form-control group-settings-field"  field="Group_Css_Id">
                    </div>
                </div>

                <div class="group-message" style>
                    <div class="alert alert-default">
                        <span>Geen groep geselecteerd</span>
                    </div>
                </div>
            </div>
            <div id="block-tab" class="tab-pane active">
                <div class="block-settings" style="display: none">
                    <h3>Block</h3>
                    <table class="table">
                        <tr>
                            <th>Type:</th>
                            <td class="type"></td>
                        </tr>
                    </table>
                    <label>Achterground afbeelding</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control block-settings-field" id="content-background-img" field="Content_Css_Background_Img">
                        <div class="input-group-append">
                            <button class="btn btn-default" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <label>Achterground kleur</label>
                    <div class="input-group mb-3">
                        <input type="color" class="form-control block-settings-field" id="content-background-color" field="Content_Css_Background_Color">
                        <div class="input-group-append">
                            <button class="btn btn-default" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-settings-items-control">
                        <div class="form-group">
                            <label>Block Grid</label>
                            <div class="btn-group btn-group-toggle Content-Rater-Selector" data-toggle="buttons">
                                <label class="btn btn-primary">
                                    <input type="radio" autocomplete="off" content-raster="2"> 2
                                </label>
                                <label class="btn btn-primary">
                                    <input type="radio" autocomplete="off" content-raster="3"> 3
                                </label>
                                <label class="btn btn-primary">
                                    <input type="radio" autocomplete="off" content-raster="4"> 4
                                </label>
                                <label class="btn btn-primary">
                                    <input type="radio" autocomplete="off" content-raster="6"> 6
                                </label>
                                <label class="btn btn-primary">
                                    <input type="radio" autocomplete="off" content-raster="12"> 12
                                </label>
                            </div>
                        </div>
                        <button class="btn btn-default" onclick="Sidebar.Block.Edit.Start()">
                            Item's aanpassen
                        </button>
                    </div>

                </div>
                <div class="block-message" style>
                    <div class="alert alert-default">
                        <span>Geen block geselecteerd</span>
                    </div>
                </div>
            </div>
        </div>
    </aside>
{/block}