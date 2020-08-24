{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/winkel/evenementen">Evenementen</a></li>
    <li class="breadcrumb-item active">{$aEvent.Event_Name}</li>
{/block}
{block content}
    <ul class="nav nav-tabs" role="tablist">
        {if $aPermissions.SHOP_EVENTS_MANAGEMENT}
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#gegevens" role="tab">Gegevens</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tickets-tabe" role="tab">Tickets</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#delete-event-tap" role="tab">Verwijderen</a>
            </li>
        {/if}
        {if $aPermissions.SHOP_EVENTS_REGISTRATION}
            <li class="nav-item">
                <a class="nav-link" href="?t=registrations" role="tab">Aanmeldingen</a>
            </li>
        {/if}
        {if $aPermissions.SHOP_EVENTS_VALIDATION}
            <li class="nav-item">
                <a class="nav-link active" href="?t=validation">Valideren</a>
            </li>
        {/if}
    </ul>
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
            <div class="card card-primary">
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
            <div class="card card-secondary">
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