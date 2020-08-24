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
                <a class="nav-link active" href="?t=registrations" role="tab">Aanmeldingen</a>
            </li>
        {/if}
        {if $aPermissions.SHOP_EVENTS_VALIDATION}
            <li class="nav-item">
                <a class="nav-link" href="?t=validation">Valideren</a>
            </li>
        {/if}
    </ul>
    <script>
        aRegistrations = {$aRegistrations};
    </script>


    <div class="card card-primary">
        <div class="card-body table-responsive">
            <table class="table table-striped registrations-table">
                <thead>
                <tr>
                    <th></th>
                    <th>Voornaam</th>
                    <th>Achternaam</th>
                    <th>E-mailadres</th>
                    <th>Telefoonnummer</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="template-ticket"  style="display: none">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Ticket nummer</th>
                <th>Naam</th>
                <th>Omschrijving</th>
                <th>Prijs</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
{/block}