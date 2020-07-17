<table>
    <tr>
        <td align="center">
            <img src="{$aWebsite.logo}" class="logo">
            <h1>{$aWebsite.name}</h1>
        </td>
    </tr>
    <tr>
        <td class="section">
            <h2>Evenement</h2>
            <table class="table">
                <tr>
                    <th>Evenement</th>

                    <th>Start tijd</th>

                    <th>Eind tijd</th>

                    <th>Locatie</th>
                </tr>
                <tr>
                    <td>{$aEvent.Event_Name}</td>

                    <td>{$aEvent.Event_Start_Timestamp|date_format:"%H:%M %d-%m-%Y"}</td>

                    <td>{$aEvent.Event_End_Timestamp|date_format:"%H:%M %d-%m-%Y"}</td>

                    <td>
                        <a href="https://www.google.nl/maps?q={$aEvent.Event_Location}">{$aEvent.Event_Location}</a>
                    </td>
                </tr>
            </table>
            <h2>Ticket</h2>
            <table class="table">
                <tr>
                    <th>Naam</th>
                    <th>Omschrijving</th>
                    <th>Ticket prijs</th>
                </tr>
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
                </tr>
            </table>
            <table>
                <tr>
                    <td>
                        <img src="{$Qr}">
                        <p style="text-align: center">{$sHash}</p>
                    </td>
                    <td colspan="2">
                        <h3>Toegang tot het evenement</h3>
                        <p>Laat deze QR code scannen op het evenement voor toegang</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="section">
            <h2>Voorwaarden</h2>
            <p>Dit ticket is niet inwisselbaar voor geld, voor een ander soort ticket dat geldig is in een andere periode of anderszins.</p>
            <p>Het ticket mag niet worden doorverkocht of anderszins voor commerciële doeleinden worden gebruikt.</p>
            <p>Het ticket moet op verzoek getoond worden.</p>
            <p>Het herroepingsrecht is van toepassing op de online aankoop via <a href="{$aWebsite.url}">{$aWebsite.url}</a>: binnen 14 dagen na aankoop kan het ongebruikte ticket geretourneerd worden aan de {$aWebiste.name} en wordt het aankoopbedrag teruggestort.</p>
            <p>Toetreden tot het evenement is op eigen risico. {$aWebsite.name} is niet aansprakelijk voor verlies, diefstal en/of beschadigingen.</p>
        </td>
    </tr>
</table>

