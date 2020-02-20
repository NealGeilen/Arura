{extends "../../index.tpl"}
{block content}
    <div class="card">
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="form-group col-6">
                        <label>Start datum</label>
                        <input type="datetime-local" class="form-control">
                    </div>
                    <div class="form-group col-6">
                        <label>Eind datum</label>
                        <input type="datetime-local" class="form-control">
                    </div>
                    <div class="form-group col-2">
                        <input type="submit" class="btn btn-secondary w-100">
                    </div>
                </div>
            </form>
        </div>
    </div>
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Bezoekers per land</h3>
            </div>
            <div class="card-body">
                <div id="container" style="position: relative; width: 700px; height: 400px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aantal apparaten</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs"  role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="pill" href="#devices-chart" role="tab">Grafiek</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#devices-table" role="tab">Tabel</a>
                    </li>
                </ul>
                <div class="tab-content" id="custom-content-below-tabContent">
                    <div class="tab-pane fade active show" id="devices-chart" role="tabpanel">
                        <canvas class="devices-chart"></canvas>
                    </div>
                    <div class="tab-pane fade" id="devices-table" role="tabpanel">
                        <table class="devices-table table">
                            <thead>
                            <tr>
                                <th>Soort</th>
                                <th>Hoeveelheid</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Exit pagina's</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs"  role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="pill" href="#exit-chart" role="tab">Grafiek</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#exit-table" role="tab">Tabel</a>
                    </li>
                </ul>
                <div class="tab-content" id="custom-content-below-tabContent">
                    <div class="tab-pane fade active show" id="exit-chart" role="tabpanel">
                        <canvas class="exit-chart"></canvas>
                    </div>
                    <div class="tab-pane fade" id="exit-table" role="tabpanel">
                        <table class="exit-table table">
                            <thead>
                            <tr>
                                <th>Pagina</th>
                                <th>Aantal keer</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Gemiddeld leestijd per pagina</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs"  role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="pill" href="#readtime-chart" role="tab">Grafiek</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#readtime-table" role="tab">Tabel</a>
                    </li>
                </ul>
                <div class="tab-content" id="custom-content-below-tabContent">
                    <div class="tab-pane fade active show" id="readtime-chart" role="tabpanel">
                        <canvas class="readtime-chart"></canvas>
                    </div>
                    <div class="tab-pane fade" id="readtime-table" role="tabpanel">
                        <table class="readtime-table table">
                            <thead>
                            <tr>
                                <th>Pagina</th>
                                <th>Tijd</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Bezoekers uit verschillende media</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs"  role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="pill" href="#media-chart" role="tab">Grafiek</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#media-table" role="tab">Tabel</a>
                    </li>
                </ul>
                <div class="tab-content" id="custom-content-below-tabContent">
                    <div class="tab-pane fade active show" id="media-chart" role="tabpanel">
                        <canvas class="media-chart"></canvas>
                    </div>
                    <div class="tab-pane fade" id="media-table" role="tabpanel">
                        <table class="media-table table">
                            <thead>
                            <tr>
                                <th>Bron</th>
                                <th>Aantallen</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}