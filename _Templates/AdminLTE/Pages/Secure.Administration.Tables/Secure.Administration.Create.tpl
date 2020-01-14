<div class="card">
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
        <form action="/{$aArura.dir}/api/secureadmin/secureadmin.php?type" id="file-upload" class="dropzone">
            <div class="fallback">
                <input name="file" type="file" multiple accept="application/json"/>
            </div>
        </form>
    </div>
</div>