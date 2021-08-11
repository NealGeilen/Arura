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
                    <input type="submit" class="btn btn-secondary mt-2">
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-info">
            <div class="card-body">
                <p>Pagina:</p>
                <h4 class="text-white">{$sPageUrl}</h4>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card bg-primary VisitorsDays">
            <div class="card-body">
                <div class="tab-pane active position-relative" id="tab-VisitorsDays-1">
                    <h3 class="card-title text-white">Bezoekers per dag</h3>
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
        <div class="card bg-secondary">
            <div class="card-body">
                <h3 class="card-title text-white">Apparaten</h3>
                <canvas class="devices-chart"></canvas>
                <!-- /.tab-content -->
            </div><!-- /.card-body -->
            <div class="overlay">
                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-secondary">
            <div class="card-body">
                <h3 class="card-title text-white">Bronnen</h3>
                <canvas class="media-chart"></canvas>
                <!-- /.tab-content -->
            </div><!-- /.card-body -->
            <div class="overlay">
                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>
    </div>
</div>