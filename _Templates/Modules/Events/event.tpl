<section>
    <div class="event-banner" style="background-image: url('{$Event->getImg()}')">
        <h1 class="text-center event-name">{$Event->getName()}</h1>
    </div>
    <div class="event-description">
        {$Event->getDescription()}
    </div>
    <div class="event-details">
        <h3>Startdatum</h3>
        <p>
            {$Event->getStart()|date_format:"%H:%M %d-%m-%Y"}
        </p>
        <h3>Einddatum</h3>
        <p>
            {$Event->getEnd()|date_format:"%H:%M %d-%m-%Y"}
        </p>
        <h3>
            Locatie
        </h3>
        <p>
            <a href="https://www.google.nl/maps?q={$Event->getLocation()}" target="_blank">{$Event->getLocation()}</a>
        </p>
    </div>
    {if $Event->isCanceled()}
        <div class="alert alert-danger bg-danger text-white rounded">
            <h1 class="alert-heading">Evenement geannuleerd!</h1>
            {$Event->getCancelReason()}
        </div>
    {elseif $Event->hasEventTickets() && $Event->getStart()->getTimestamp() >= $smarty.now}
        <h2>Tickets</h2>
        <form method="post" action="/event/{$Event->getSlug()}/checkout" class="form-event-order">
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
                        {foreach $Event->getTickets() as $Ticket}
                            <tr>
                                <td>{$Ticket->getName()}</td>
                                <td>{$Ticket->getDescription()}</td>
                                <td>€ {$Ticket->getPrice()|number_format:2:",":"."}</td>
                                <td>
                                    {if $Event->getIsActive()}
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Aantal</span>
                                            </div>
                                            <input type="number" class="form-control text-white ticket-amount" placeholder="Aantal" min="0" name="Tickets[{$Ticket->getId()}]" max="{$Ticket->getCapacity() - $Ticket->getBoughtTickets()}">
                                        </div>
                                    {elseif $Ticket->getCapacity() < $Ticket->getBoughtTickets()}
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
                {if $Event->getIsActive()}
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
    {elseif $Event->getStart()->getTimestamp() >= $smarty.now && $Event->getEndRegistration()->getTimestamp() >= $smarty.now}
            {if !$Event->getIsActive() || !$Event->getIsVisible()}
                <div class="event-signup-holdup">
                    <div class="alert alert-info">
                        Inschrijven nog niet mogelijk.
                    </div>
                </div>
            {elseif $Event->getCapacity() <= $Event->getRegisteredAmount()}
                <div class="event-signup-holdup">
                    <div class="alert alert-info">
                        Inschrijven niet meer mogelijk, Evenment is vol
                    </div>
                </div>
            {/if}
            <h2>Inschrijven</h2>
        <form class="event-signup" method="post" action="/event/{$Event->getSlug()}">
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
            <input type="hidden" name="Event-Signup">
            <div class="form-row">
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
                    <input type="number" name="amount" class="form-control" min="1" value="1" required max="{$Event->getCapacity() - $Event->getRegisteredAmount()}">
                    <div class="help-block with-errors"></div>
                </div>
            </div>
            {$form}
            <input type="submit" class="btn btn-primary" value="Inschrijven">
        </form>
    {else}
        <div class="alert alert-info text-center">
            Inschrijving gesloten
        </div>
    {/if}


</section>