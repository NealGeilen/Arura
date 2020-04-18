{extends "../../../index.tpl"}
{block content}
    <script>
        aRegistrations = {$aRegistrations};
    </script>


    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Aanmeldingen</h3>
        </div>
        <div class="card-body table-responsive">
            <table class="table registrations-table">
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