{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/winkel/evenementen">Evenementen</a></li>
    <li class="breadcrumb-item active">{$Event->getName()}</li>
{/block}
{block content}
    {assign var="tabsType" value="tickets"}
    {include file="./../Events/tabs.tpl"}
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