<ul class="nav nav-tabs" role="tablist">
    {if $Permissions.SHOP_EVENTS_MANAGEMENT}
        <li class="nav-item">
            <a class="nav-link{if $tabsType=== "tabs"} active{/if}" data-toggle="tab" href="#gegevens" role="tab">Gegevens</a>
        </li>
        {if ($Event->hasEventRegistrations() && $Event->hasEventTickets()) || !$Event->hasEventRegistrations()}
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tickets-tabe" role="tab">Tickets</a>
            </li>
        {/if}
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#delete-event-tap" role="tab">Verwijderen</a>
        </li>
        {if !$Event->hasEventRegistrations()}
            <li class="nav-item">
                <a class="nav-link{if $tabsType=== "form"} active{/if}"  href="?t=form" role="tab">Registartie formulier <span class="badge badge-beta">Beta</span></a>
            </li>
        {/if}
        <li class="nav-item">
            <a class="nav-link{if $tabsType=== "analytics"} active{/if}" href="?t=analytics" role="tab">Analytics</a>
        </li>
    {/if}
    {if $Permissions.SHOP_EVENTS_REGISTRATION}
        <li class="nav-item">
            <a class="nav-link{if $tabsType=== "tickets"} active{/if}" href="?t=registrations" role="tab">Aanmeldingen</a>
        </li>
    {/if}
    {if $Permissions.SHOP_EVENTS_VALIDATION && $Event->hasEventTickets() && !$Event->isCanceled()}
        <li class="nav-item">
            <a class="nav-link{if $tabsType=== "validate"} active{/if}" href="?t=validation">Valideren</a>
        </li>
    {/if}
</ul>