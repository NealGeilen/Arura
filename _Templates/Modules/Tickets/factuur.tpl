<table>
    <tr>
        <td align="center">
            <img src="{$aWebsite.logo}" class="logo">
            <h1>{$aWebsite.name}</h1>
            <h2>Factuur</h2>
        </td>
    </tr>
    <tr>
        <td class="section">
            <table class="table">
                <tr>
                    <th>
                        Naam
                    </th>
                    <th>
                        Omschrijving
                    </th>
                    <th>
                        Prijs per stuk
                    </th>
                    <th>
                        Aantal
                    </th>
                    <th>
                        Totaal
                    </th>
                </tr>
                {assign var="Amount" value="0"}
                {foreach from=$aTickets.Tickets item=aTicket}
                <tr>
                    <td>
                        {$aTicket.Ticket_Name}
                    </td>
                    <td>
                        {$aTicket.Ticket_Description}
                    </td>
                    <td>
                        € {$aTicket.Ticket_Price|number_format:2:",":"."}
                    </td>
                    <td>
                        {$aTicket.Amount}
                    </td>
                    <td>
                        € {($aTicket.Amount * $aTicket.Ticket_Price)|number_format:2:",":"."}{$Amount = $Amount + ($aTicket.Amount * $aTicket.Ticket_Price)}
                    </td>
                </tr>
                {/foreach}
                {if !empty($btwPer)}
                <tr>
                    <td colspan="2"></td>
                    <th colspan="2">BTW {$btwPer}%</th>
                    <th>€ {($Amount * ($btwPer / 100))|number_format:2:",":"."}</th>
                </tr>
                {/if}
                <tr>
                    <td colspan="2"></td>
                    <th colspan="2">Totaal betaald bedrag</th>
                    <th>€ {$Amount|number_format:2:",":"."}</th>
                </tr>
            </table>
        </td>
    </tr>
</table>

