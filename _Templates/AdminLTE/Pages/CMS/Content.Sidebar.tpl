<ul class="nav nav-tabs">
    <li class="nav-item">
        <a href="#block-tab" data-toggle="tab" class="text-center active" aria-expanded="true"><i class="fas fa-square"></i> Block</a>
    </li>
    <li class="nav-item">
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