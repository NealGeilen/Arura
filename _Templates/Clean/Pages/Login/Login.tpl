{extends "../../index.tpl"}
{block content}
  <div class="login-box">
    <div class="login-logo">
      <img src="{$aWebsite.logo}">
      <b>{$aWebsite.name}</b>
    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        {if $canUserLogin}
          <p class="login-box-msg">Inloggen</p>

          {$form}
          {else}
          <p>Je hebt herhaaldelijk geprobeert in te loggen. Probeer het later opnieuw.</p>
        {/if}

        <p class="mb-1">
          <a href="javascript:sendRecoveryMail()">Wachtwoord vergeten?</a>
        </p>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
  <!-- /.login-box -->
  <div class="modal modal-recovery-mail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered " role="document">
      <div class="modal-content">
        <form>
          <div class="modal-header">
            <h5 class="modal-title">Wachtwoord vergeten</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-row">
              <div class="col-12">
                <div class="form-group">
                  <input class="form-control" type="email" name="email" placeholder="Email">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Email verzenden</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
          </div>
        </form>
      </div>
    </div>
  </div>
{/block}
