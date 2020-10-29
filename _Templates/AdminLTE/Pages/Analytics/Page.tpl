<div class="row">
    <div class="col-md-6">
        <div class="card analyticspage-page">
            <div class="card-body">
                <form class="form-dates">
                    <div class="row">
                        <div class="form-group col-6">
                            <label>Start datum</label>
                            <input name="startDate" type="date" max="{$smarty.now|date_format:"Y-m-d"}" required class="form-control" value="{($smarty.now - 24*60*60*7)|date_format:"Y-m-d"}">
                        </div>
                        <div class="form-group col-6">
                            <label>Eind datum</label>
                            <input name="endDate" type="date" required class="form-control" value="{$smarty.now|date_format:"Y-m-d"}" max="{$smarty.now|date_format:"Y-m-d"}">
                        </div>
                    </div>
                    <input type="submit" class="btn btn-secondary">
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h4>{$sPageUrl}</h4>

                <p>Pagina</p>
            </div>
            <div class="icon">
                <i class="fas fa-globe-europe"></i>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card card-primary VisitorsDays">
            <div class="card-header">
                <h3 class="card-title">Bezoekers per dag</h3>
            </div><!-- /.card-header -->
            <div class="card-body bg-primary">
                <div class="tab-pane active position-relative" id="tab-VisitorsDays-1">
                    <canvas></canvas>
                </div>
                <!-- /.tab-content -->
            </div><!-- /.card-body -->
            <div class="overlay">
                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Apparaten</h3>
            </div><!-- /.card-header -->
            <div class="card-body bg-secondary">
                <canvas class="devices-chart"></canvas>
                <!-- /.tab-content -->
            </div><!-- /.card-body -->
            <div class="overlay">
                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Bronnen</h3>
            </div><!-- /.card-header -->
            <div class="card-body bg-secondary">
                <canvas class="media-chart"></canvas>
                <!-- /.tab-content -->
            </div><!-- /.card-body -->
            <div class="overlay">
                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>
    </div>
</div>