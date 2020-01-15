<div class="login-box">
    <div class="login-logo">
        <img src="{$aWebsite.logo}">
        <p>Ticket validatie</p>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="text-center"><b>{$aTicket.OrderedTicket_Hash}</b></p>
            <p>Ticket is legitiem</p>
            {$aTicket|var_dump}
        </div>
        <!-- /.login-card-body -->
    </div>
</div>