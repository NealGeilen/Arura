{extends "../../index.tpl"}
{block content}
    <div class="login-box">
        <div class="login-logo">
            <b>{$aWebsite.name}</b>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Je hebt bijna een nieuw wachtwoord nog een paar stappen.</p>

                {$form}
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->


{/block}