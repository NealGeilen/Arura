{extends "$TEMPLATEDIR/index.tpl"}
{block content}
    <div class="login-box">
        <div class="login-logo">
            <b>{$aWebsite.name}</b>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Je hebt bijna een nieuw wachtwoord nog een paar stappen.</p>

                <form action="/{$aArura.dir}/{$aArura.api}/user/passwordrecovery.php?type=set-password" method="post" class="form-create-new-pass">
                    <input type="hidden" name="Token" value="{$smarty.GET.i}">
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Wachtwoord" name="Password1">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Bevestig wachtwoord" name="Password2">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Wachtwoord veranderen</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->


{/block}