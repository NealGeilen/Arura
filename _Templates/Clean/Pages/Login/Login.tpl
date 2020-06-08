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

          {$loginForm}
          {else}
          <p>Je hebt herhaaldelijk geprobeert in te loggen. Probeer het later opnieuw.</p>
        {/if}

        <p class="mb-1">
          <a href="#" data-toggle="modal" data-target=".modal-recovery-mail">Wachtwoord vergeten?</a>
        </p>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
  <!-- /.login-box -->
  <div class="modal modal-recovery-mail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered " role="document">
      <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Wachtwoord vergeten</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            {$recoverForm}
          </div>
      </div>
    </div>
  </div>
{/block}

{block jsPage}
  <script>
    {if $recoverFormHasError}
      $(".modal-recovery-mail").modal("show");
    {/if}
  </script>
{/block}