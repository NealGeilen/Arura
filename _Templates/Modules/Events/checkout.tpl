<section>
    <form method="post" action="/event/{$Event->getSlug()}/payment" class="form-event-checkout">
        <h2>Gegevens</h2>
        {if isset($isSuccess)}
            {if $isSuccess}
                <div class="alert alert-success">
                    Inschrijving succesvol
                </div>
            {else}
                <div class="alert alert-danger">
                    Er is iets misgegaan. De aanmelding is niet succesvol
                </div>
            {/if}
        {/if}
        <div class="form-row">
            <input type="hidden" name="id" required value="{$Event->getId()}">
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
        {$form}
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
                {foreach from=$collection.Tickets item=Ticket}
                    <tr>
                        <td>{$Ticket->getName()}
                        <td>{$Ticket->Amount}</td>
                        <td>€{$Ticket->getPrice()|number_format:2:",":"."}</td>
                        <td>€{($Ticket->getPrice() * {$Ticket->Amount})|number_format:2:",":"."}</td>
                    </tr>
                {/foreach}
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <th>Totaal bedrag</th>
                    <th>€{$collection.Amount|number_format:2:",":"."}</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <hr/>
        <h2>Selecteer uw bank</h2>
        <div class="row text-center">
            {foreach from=$Issuers item=Issuer}
            <div class="col-md-4 col-6">
                <label class="bank-select">
                    <img src="{$Issuer.image.size2x}">
                    {$Issuer.name}
                    <input type="radio" name="issuer" value="{$Issuer.id}" required>
                </label>
            </div>
            {/foreach}
        </div>
        <div class="bank-select-error">

        </div>
        <input type="submit" class="btn btn-primary" value="Betalen">
    </form>
</section>