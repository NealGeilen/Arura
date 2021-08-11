{extends "../../security.tpl"}
{block content}
  <div class="container">
      <img src="{$aWebsite.logo}" style="max-width: 100%; max-height: 100px; margin: 0 auto; display: block">
      <h1 class="text-center w-100 text-white m-1">{$aWebsite.name}</h1>
      <div class="card">
          <div class="card-body">
              {if $canUserLogin}
                  <h2>Inloggen</h2>
                  {$loginForm->startForm()}
                  {$loginForm->getControl("mail")}
                  {$loginForm->getControl("password")}
                  {$loginForm->getControl("submit")}
                  {$loginForm->endForm()}
              {else}
                  <h3>Je hebt herhaaldelijk geprobeert in te loggen. Probeer het later opnieuw.</h3>
              {/if}
          </div>
      </div>
      <div class="card">
          <div class="card-body">
              <a href="#" data-toggle="modal" data-target=".modal-recovery-mail">Wachtwoord vergeten?</a>
          </div>
      </div>
  </div>
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