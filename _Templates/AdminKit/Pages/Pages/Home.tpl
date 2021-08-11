{extends "../../index.tpl"}
{block content}
    <script>
        var JSONUserActions = '{$JSONUserActions}';
        var JSONEventRegistrations = '{$JSONEventRegistrations}';
        var JSONPayments = '{$JSONPayments}';
        var JSONLogs = '{$JSONLogs}';
    </script>
    <div class="row">
        <div class="col-12">
            <div class="card bg-primary">
                <div class="card-body position-relative">
                    <canvas class="TimeLine d-inline"></canvas>
                </div>
            </div>
        </div>
    </div>
{/block}