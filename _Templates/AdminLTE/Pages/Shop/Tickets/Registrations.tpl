{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/winkel/evenementen">Evenementen</a></li>
    <li class="breadcrumb-item active">{$Event->getName()}</li>
{/block}
{block content}
    {assign var="tabsType" value="tickets"}
    {include file="./../Events/tabs.tpl"}
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
                    {foreach $Fields as $Field}
                        <th>{$Field->getTitle()|escape:'html'}</th>
                    {/foreach}
                    <th>Inschrijf datum</th>
                    <th>Aantal</th>
                </tr>
                </thead>
                <tbody>
                {foreach $Event->getRegistration() as $Registration}
                    <tr>
                        <td>{$Registration->getFirstname()|escape:'html'}</td>
                        <td>{$Registration->getLastname()|escape:'html'}</td>
                        <td>{$Registration->getEmail()|escape:'html'}</td>
                        <td>{$Registration->getTel()|escape:'html'}</td>
                        {foreach $Fields as $Field}
                            <td>{$Registration->getAdditionalField($Field->getTag())|escape:'html'}</td>
                        {/foreach}
                        <td>{$Registration->getSignUpTime()->getTimestamp()|date_format:"%H:%M %d-%m-%Y"}</td>
                        <td>{$Registration->getAmount()|escape:'html'}</td>
                        {$iRegistrationAmount = $iRegistrationAmount + $Registration->getAmount()}
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

{/block}
