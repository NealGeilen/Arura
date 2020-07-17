<section class="text-center">
    {if $sStatus === "paid"}
    <h1>Bedankt voor je bestelling</h1>
    <p>Je hebt zojuist tickets besteld voor: {$aEvent.Event_Name}.</p>
    <p>De tickets zijn zojuist verzonden naar je mailbox.</p>
    <p>We zijn je graag komen op: {$aEvent.Event_Start_Timestamp|date_format:"%d-%m-%Y"}.</p>
    {/if}
    {if $sStatus === "canceled"}
    <h1>Je hebt de bestelling geannuleerd</h1>
    <p>Wil je de tickets toch bestellen? klik dan <a href="{$aWebsite}/event/{$aEvent.Event_Slug}">Hier</a>.</p>
    {/if}
    {if $sStatus === "pending"}
    <h1>De bestelling is nog niet voltooid</h1>
    {/if}
    {if $sStatus === "authorized"}
    <h1>De bestelling is goed gekeurt. Wacht nog een ogenblik.</h1>
    {/if}
    {if $sStatus === "expired"}
    <h1>Dit betaal verzoek is verlopen.</h1>
    <p>Wil je de tickets toch bestellen? klik dan <a href="{$aWebsite}/event/{$aEvent.Event_Slug}">Hier</a>.</p>
    {/if}
    {if $sStatus === "failed"}
    <h1>Door een technisch mankement is de betaling niet voltooid.</h1>
    <p>Probeer alsjeblieft de betaling opnieuw in te vullen <a href="{$aWebsite}/event/{$aEvent.Event_Slug}">{$aEvent.Event_Name}</a>.</p>
    {/if}
</section>