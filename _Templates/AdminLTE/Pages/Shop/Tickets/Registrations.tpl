{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/winkel/evenementen">Evenementen</a></li>
    <li class="breadcrumb-item active">{$Event->getName()}</li>
{/block}
{block content}
    {assign var="tabsType" value="tickets"}
    {include file="./../Events/tabs.tpl"}
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-body table-responsive">
                    {assign var="iRegistrationAmount" value="0"}
                    <table class="table table-striped Arura-Table">
                        <thead>
                        <tr>
                            <th>Voornaam</th>
                            <th>Achternaam</th>
                            <th>E-mailadres</th>
                            <th>Telefoonnummer</th>
                            <th>Inschrijf datum</th>
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
                                <td>{$aRegistration.Registration_Timestamp|date_format:"%H:%M %d-%m-%Y"}</td>
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
                            <td></td>
                            <td>Totaal:</td>
                            <td><b>{$iRegistrationAmount}</b></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

{/block}

{block JsPage}
    <script>
    </script>
{/block}