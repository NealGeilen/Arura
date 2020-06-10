{extends "../../index.tpl"}
{block content}
    <div class="card card-primary card-outline">
        <div class="card-body">
            <form class="form-dates">
                <div class="row">
                    <div class="form-group col-6">
                        <label>Start datum</label>
                        <input name="startDate" type="date" required class="form-control" value="{($smarty.now - 24*60*60*14)|date_format:"Y-m-d"}">
                    </div>
                    <div class="form-group col-6">
                        <label>Eind datum</label>
                        <input name="endDate" type="date" required class="form-control" value="{$smarty.now|date_format:"Y-m-d"}">
                    </div>
                </div>
                <input type="submit" class="btn btn-secondary">
            </form>
        </div>
    </div>
{*<div class="row">*}
{*    <div class="col-lg-6">*}
{*        <div class="card">*}
{*            <div class="card-header">*}
{*                <h3 class="card-title">Bezoekers per land</h3>*}
{*                <div class="card-tools">*}
{*                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>*}
{*                    </button>*}
{*                </div>*}
{*            </div>*}
{*            <div class="card-body">*}
{*                <div id="container" style="position: relative; width: 700px; height: 400px; max-width: 100%"></div>*}
{*            </div>*}
{*        </div>*}
{*    </div>*}
{*</div>*}
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="card card-secondary card-outline">
                <div class="card-header d-flex p-0">
                    <h3 class="card-title p-3">Soorten apparaten</h3>
                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item"><a class="nav-link active" href="#tab-devices-1" data-toggle="tab"><i class="fas fa-chart-pie"></i></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab-devices-2" data-toggle="tab"><i class="fas fa-table"></i></a></li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-card-widget="collapse"><i class="fas fa-minus"></i></a>
                        </li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-devices-1">
                            <canvas class="devices-chart"></canvas>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab-devices-2">
                            <table class="devices-table table">
                                <thead>
                                <tr>
                                    <th>Soort</th>
                                    <th>Hoeveelheid</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="card card-secondary card-outline">
                <div class="card-header d-flex p-0">
                    <h3 class="card-title p-3">Gemiddeld leestijd per pagina</h3>
                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item"><a class="nav-link active" href="#tab-readtime-1" data-toggle="tab"><i class="fas fa-chart-pie"></i></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab-readtime-2" data-toggle="tab"><i class="fas fa-table"></i></a></li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-card-widget="collapse"><i class="fas fa-minus"></i></a>
                        </li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-readtime-1">
                            <canvas class="readtime-chart"></canvas>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab-readtime-2">
                            <table class="readtime-table table">
                                <thead>
                                <tr>
                                    <th>Pagina</th>
                                    <th>Tijd</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="card card-secondary card-outline">
                <div class="card-header d-flex p-0">
                    <h3 class="card-title p-3">Exit pagina's</h3>
                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item"><a class="nav-link active" href="#tab-exit-1" data-toggle="tab"><i class="fas fa-chart-pie"></i></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab-exit-2" data-toggle="tab"><i class="fas fa-table"></i></a></li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-card-widget="collapse"><i class="fas fa-minus"></i></a>
                        </li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-exit-1">
                            <canvas class="exit-chart"></canvas>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab-exit-2">
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
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="card card-secondary card-outline">
                <div class="card-header d-flex p-0">
                    <h3 class="card-title p-3">Bezoekers vanuit verschillinde media</h3>
                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item"><a class="nav-link active" href="#tab-media-1" data-toggle="tab"><i class="fas fa-chart-pie"></i></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab-media-2" data-toggle="tab"><i class="fas fa-table"></i></a></li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-card-widget="collapse"><i class="fas fa-minus"></i></a>
                        </li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-media-1">
                            <canvas class="media-chart"></canvas>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab-media-2">
                            <table class="media-table table">
                                <thead>
                                <tr>
                                    <th>Bron</th>
                                    <th>Aantallen</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
        </div>
    </div>
{/block}