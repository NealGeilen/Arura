<section>
    <form method="post" action="{$aWebsite.url}/event/{$smarty.SERVER.REDIRECT_URL}/payment" class="form-event-checkout">
        <h2>Gegevens</h2>
        <div class="form-row">
            <input type="hidden" name="id" required value="{$aEvent.Event_Id}">
            <div class="form-group col-6">
                <label>Voornaam</label>
                <input type="text" name="firstname" class="form-control" required>
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group col-6">
                <label>Achternaam</label>
                <input type="text" name="lastname" class="form-control" required>
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group col-6">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group col-6">
                <label>Telefoonnummer</label>
                <input type="text" name="tel" class="form-control" required>
                <div class="help-block with-errors"></div>
            </div>
        </div>
        <hr/>
        <h2>Tickets</h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Aantal</th>
                        <th>Prijs p.st.</th>
                        <th>Prijs totaal</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$aTickets item=aTicket}
                    <tr>
                        <td>{$aTicket.Ticket_Name}<input type="hidden" value="{$aTicket.Amount}" required name="Tickets[{$aTicket.Ticket_Id}]"></td>
                        <td>{$aTicket.Amount}</td>
                        <td>€{$aTicket.Ticket_Price|number_format:2:",":"."}</td>
                        <td>€{($aTicket.Ticket_Price * $aTicket.Amount)|number_format:2:",":"."}</td>
                    </tr>
                {/foreach}
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <th>Totaal bedrag</th>
                    <th>€{$iTotalAmount|number_format:2:",":"."}</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <hr/>
        <h2>Selecteer uw bank</h2>
        <div class="row text-center">
            {foreach from=$aIssuers item=aIssuer}
            <div class="col-md-4 col-6">
                <label class="bank-select">
                    <img src="{$aIssuer.image.size2x}">
                    {$aIssuer.name}
                    <input type="radio" name="issuer" value="{$aIssuer.id}" required>
                </label>
            </div>
            {/foreach}
        </div>
        <div class="bank-select-error">

        </div>
        <input type="submit" class="btn btn-primary" value="Betalen">
    </form>
</section>