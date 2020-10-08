{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/administration">Beveiligde administartie</a></li>
    <li class="breadcrumb-item active">Administartie aanmaken</li>
{/block}


{block content}
    <div class="card card-primary">
        <div class="card-header">
            <h2 class="card-title">Administartie aanmaken</h2>
            <div class="card-tools">
                <div class="btn-group">
                    <a class="btn btn-primary" href="/{$aArura.dir}/administration"><i class="fas fa-long-arrow-alt-left"></i></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <label>Data bestand</label>
            <form action="/dashboard/administration/create" id="file-upload" class="dropzone"></form>
        </div>
    </div>
{/block}

{block JsPage}
    <script>
        $(".dropzone").dropzone();
    </script>
{/block}
