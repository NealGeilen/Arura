<section>
    <div class="event-banner" style="background-image: url('{$aEvent.Event_Banner}')">
        <h1 class="text-center event-name">{$aEvent.Event_Name}</h1>
    </div>
    <div class="event-description">
        {$aEvent.Event_Description}
    </div>
    <div class="event-details">
        <h3>Startdatum</h3>
        <p>
            {$aEvent.Event_Start_Timestamp|date_format:"%H:%M %d-%m-%Y"}
        </p>
        <h3>Einddatum</h3>
        <p>
            {$aEvent.Event_End_Timestamp|date_format:"%H:%M %d-%m-%Y"}
        </p>
        <h3>
            Locatie
        </h3>
        <p>
            <a href="https://www.google.nl/maps?q={$aEvent.Event_Location}" target="_blank">{$aEvent.Event_Location}</a>
        </p>
    </div>
    {if $Event->isCanceled()}
        <div class="alert alert-danger bg-danger text-white rounded">
            <h1 class="alert-heading">Evenement geannuleerd!</h1>
            {$Event->getCancelReason()}
        </div>
        {else}
        {if $aTickets !== [] && $aEvent.Event_Registration_End_Timestamp > $smarty.now}
            <h2>Tickets</h2>
            <form method="post" action="{$aWebsite.url}/event/{$smarty.SERVER.REDIRECT_URL}/checkout" class="form-event-order">
                <div class="card">
                    <div class="card-body table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Naam</th>
                                <th>Omschrijving</th>
                                <th>Prijs</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$aTickets item=aTicket}
                                <tr>
                                    <td>{$aTicket.Ticket_Name}</td>
                                    <td>{$aTicket.Ticket_Description}</td>
                                    <td>€ {$aTicket.Ticket_Price|number_format:2:",":"."}</td>
                                    <td>
                                        {if $aEvent.Event_IsActive}
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Aantal</span>
                                                </div>
                                                <input type="number" class="form-control text-white ticket-amount" placeholder="Aantal" min="0" name="Tickets[{$aTicket.Ticket_Id}]" max="{$aTicket.Ticket_Capacity - $aTicket.Count}">
                                            </div>
                                        {elseif $aTicket.Ticket_Capacity < $aTicket.Count}
                                            <div class="alert alert-info text-center">
                                                Uitverkocht
                                            </div>
                                        {else}
                                            <div class="alert alert-info text-center">
                                                Ticket nog niet beschikbaar
                                            </div>
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                        <div class="ticket-error"></div>
                    </div>
                    {if $aEvent.Event_IsActive}
                        <div class="card-footer">
                            <div class="btn-group" style="float: right">
                                <button class="btn btn-primary" type="submit">
                                    Bestellen
                                </button>
                            </div>
                        </div>
                    {/if}

                </div>
            </form>
        {elseif $aEvent.Event_Registration_End_Timestamp > $smarty.now}
            <form class="event-signup">
                {if $aEvent.Event_IsActive === 0 || $aEvent.Event_IsVisible === 0}
                    <div class="event-signup-holdup">
                        <div class="alert alert-info">
                            Inschrijven nog niet mogelijk.
                        </div>
                    </div>
                {elseif $aEvent.Event_Capacity === 0}
                    <div class="event-signup-holdup">
                        <div class="alert alert-info">
                            Inschrijven niet meer mogelijk, Evenment is vol
                        </div>
                    </div>
                {/if}
                <h2>Inschrijven</h2>
                <div class="form-row">
                    <input type="hidden" name="id" required value="{$aEvent.Event_Id}">
                    <input type="hidden" name="type" value="register-event">
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
                    <div class="form-group col-12">
                        <label>Aantal deelnemers</label>
                        <input type="number" name="amount" class="form-control" min="1" value="1" required max="{$aEvent.Event_Capacity - $iRegistartions}">
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <input type="submit" class="btn btn-primary" value="Inschrijven">
            </form>
        {else}
            <div class="alert alert-info text-center">
                Inschrijving gesloten
            </div>
        {/if}
    {/if}

</section>