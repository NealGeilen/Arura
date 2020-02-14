{extends "$TEMPLATEDIR/index.tpl"}
{block content}
    <style>
        .file{
            font-size: 24px;
            margin: 0 auto;
            text-align: center;
            display: block;
        }
    </style>
    <div class="row">
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Overzicht</h2>
                    <div class="card-tools">
                        <div class="btn-group node-options btn-group-sm">
                            <button class="btn btn-primary" onclick="Filemanger.uploadItem()" disabled>
                                <i class="fas fa-upload"></i>
                            </button>
                            <button class="btn btn-primary" onclick="Filemanger.DirThreeFunctions.CreateDir()" disabled>
                                <i class="fas fa-folder-plus"></i>
                            </button>
                            <button class="btn btn-primary" onclick="Filemanger.DirThreeFunctions.RenameItem()" disabled>
                                <i class="fas fa-pen"></i>
                            </button>
                            <button class="btn btn-primary" disabled onclick="Filemanger.DirThreeFunctions.DeleteItems()">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="FileManger-Three">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">Bestand informatie</h2>
                </header>
                <div class="card-body row">
                    <div class="col-md-6 table-responsive">
                        <table class="table">
                            <tr>
                                <th>Naam</th>
                                <td class="name"></td>
                            </tr>
                            <tr>
                                <th>Url</th>
                                <td class="url"></td>
                            </tr>
                            <tr>
                                <th>Typen</th>
                                <td class="type"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6 file">
                        <i class="fas fa-file"></i>
                    </div>
                </div>
            </section>
        </div>
    </div>
    {/block}