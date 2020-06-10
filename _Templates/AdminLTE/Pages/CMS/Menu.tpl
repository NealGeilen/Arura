{extends "../../index.tpl"}
{block content}
    <style>
        .dd{
            max-width: unset;
        }
        .dd-item{
            position: relative;
        }
        .dd-handle{
            position: absolute;
            padding: 2px 5px;
            margin: 8px;
            left: 0;
            top: 0;
            cursor: grabbing;
            height: unset;
            z-index: 20;
        }
        .dd-list{
            list-style-type: none;
        }
        .dd-empty{
            display: none;
        }
        .Nav-Item .btn{
            padding: 2px 5px;
        }
        .Nav-Item-Container{
            min-height: 30px;
        }
        .Nav-Item{
            z-index: 10;
            position: relative;
            display: block;
            height: 40px;
            margin: 5px 0;
            padding: 5px 10px 5px 40px;
            color: #333;
            text-decoration: none;
            font-weight: bold;
            border: 1px solid #ccc;
            background: #fafafa;
            background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
            background: -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
            background: linear-gradient(top, #fafafa 0%, #eee 100%);
            -webkit-border-radius: 3px;
            border-radius: 3px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        .Nav-Item .Nav-Item-Controls{
            margin: 8px;
            display: block;
            position: absolute;
            top: 0;
            right: 0;
        }
        .dd-dragel{
            display: block;
            z-index: 1000;
        }

    </style>


    <div class="row">
        <div class="col-12 ">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h2 class="card-title">Hoofd menu</h2>
                    <div class="card-tools">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-primary" onclick="creatNavBarItemModal()">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button class="btn btn-primary" onclick="save()">
                                <i class="fas fa-save"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dd" id="nestable-json"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h2 class="card-title">Sitemap</h2>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary" onclick="buildSitemap()">
                        Update sitemap
                    </button>
                    <button class="btn btn-secondary" onclick="submitSitemap()">
                        Sitemap verzenden naar Google
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="template-item" style="display: none">
        <div class="Nav-Item-Container">
            <div class="Nav-Item">
                <span class="Nav-Item-name"></span>
                <span class="Nav-Item-url"></span>
                <div class="Nav-Item-Controls">
                    <div class="btn-group-sm btn-group">
                        <button class="btn btn-primary btn-sm" onclick="editNavBarItemModal($(this))">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteItem($(this))">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="template-input" style="display: none">
        <form class="form-item">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Naam</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>Url</label>
                    <input type="text" name="url" class="form-control" required>
                </div>
            </div>
        </form>
    </div>



{/block}
