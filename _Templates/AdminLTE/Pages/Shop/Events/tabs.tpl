<div class="row">
    <div class="col-md-4 col-6">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-user-friends"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Aanmeldingen</span>
                <span class="info-box-number">{$Event->getRegisteredAmount()}</span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-globe-europe"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Pagina</span>
                <span class="info-box-number"><a href="/event/{$Event->getSlug()}" target="_blank" class="text-dark">/event/{$Event->getSlug()}</a></span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div>
</div>

<ul class="nav nav-tabs" role="tablist">
    {if $aPermissions.SHOP_EVENTS_MANAGEMENT}
        <li class="nav-item">
            <a class="nav-link{if $tabsType=== "tabs"} active{/if}" data-toggle="tab" href="#gegevens" role="tab">Gegevens</a>
        </li>
        {if ($Event->hasEventRegistrations() && $Event->hasEventTickets()) || !$Event->hasEventRegistrations()}
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tickets-tabe" role="tab">Tickets <span class="badge badge-beta">Beta</span></a>
            </li>
        {/if}
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#delete-event-tap" role="tab">Verwijderen</a>
        </li>
{*        <li class="nav-item">*}
{*            <a class="nav-link{if $tabsType=== "form"} active{/if}"  href="?t=form" role="tab">Registartie formulier <span class="badge badge-beta">Beta</span></a>*}
{*        </li>*}
    {/if}
    {if $aPermissions.SHOP_EVENTS_REGISTRATION}
        <li class="nav-item">
            <a class="nav-link{if $tabsType=== "tickets"} active{/if}" href="?t=registrations" role="tab">Aanmeldingen</a>
        </li>
    {/if}
    {if $aPermissions.SHOP_EVENTS_VALIDATION && $Event->hasEventTickets() && !$Event->isCanceld()}
        <li class="nav-item">
            <a class="nav-link{if $tabsType=== "validate"} active{/if}" href="?t=validation">Valideren</a>
        </li>
    {/if}
</ul>