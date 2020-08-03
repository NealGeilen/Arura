{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/winkel/evenementen/tickets">Evenementen</a></li>
    <li class="breadcrumb-item active">Tickets: {$aEvent.Event_Name}</li>
{/block}
{block content}
    <script>
        aRegistrations = {$aRegistrations};
    </script>


    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Aanmeldingen</h3>
        </div>
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