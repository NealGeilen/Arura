<div class="card">
    <div class="card-header">
        <h3 class="card-title">Aanmeldingen voor {$aEvent.Event_Name}</h3>
    </div>
    <div class="card-body table-responsive">
        {assign var="iRegistrationAmount" value="0"}
        <table class="table table-striped Arura-Table">
            <thead>
            <tr>
                <th>Voornaam</th>
                <th>Achternaam</th>
                <th>E-mailadres</th>
                <th>Telefoonnummer</th>
                <th>Aantal</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$aRegistrations item=aRegistration}
                <tr>
                    <td>{$aRegistration.Registration_Firstname}</td>
                    <td>{$aRegistration.Registration_Lastname}</td>
                    <td>{$aRegistration.Registration_Email}</td>
                    <td>{$aRegistration.Registration_Tel}</td>
                    <td>{$aRegistration.Registration_Amount}</td>
                    {$iRegistrationAmount = $iRegistrationAmount + $aRegistration.Registration_Amount}
                </tr>
            {/foreach}
            </tbody>
            <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>Totaal:</td>
                <td><b>{$iRegistrationAmount}</b></td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>