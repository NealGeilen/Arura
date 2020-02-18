{extends "../../index.tpl"}
{block content}
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aantal apparaten</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="form-group col-4">
                            <label>Start datum</label>
                            <input type="datetime-local" class="form-control">
                        </div>
                        <div class="form-group col-4">
                            <label>Eind datum</label>
                            <input type="datetime-local" class="form-control">
                        </div>
                        <div class="form-group col-4">
                            <input type="submit" class="btn btn-secondary w-100">
                        </div>
                    </div>
                </form>
                <canvas class="devices"></canvas>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aantal apparaten</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="form-group col-4">
                            <label>Start datum</label>
                            <input type="datetime-local" class="form-control">
                        </div>
                        <div class="form-group col-4">
                            <label>Eind datum</label>
                            <input type="datetime-local" class="form-control">
                        </div>
                        <div class="form-group col-4">
                            <input type="submit" class="btn btn-secondary w-100">
                        </div>
                    </div>
                </form>
                <canvas class="readtime"></canvas>
            </div>
        </div>
    </div>
</div>
{/block}