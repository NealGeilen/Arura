{extends "$TEMPLATEDIR/index.tpl"}
{block content}
    <style>
        video{
            width: 60%!important;
            display: block;
            margin: 0 auto;
            max-width: 100%!important;
        }
    </style>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Scanner
                    </h3>
                    <div class="card-tools">
                    </div>
                </div>
                <div class="card-body">
                    <video id="preview"></video>
                    <p id="scan-message" class="text-center"></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Gescande tickets
                    </h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped ticket-table">
                        <thead>
                        <tr>
                            <th>Nummer</th>
                            <th>Beschrijving</th>
                            <th>Event</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {/block}