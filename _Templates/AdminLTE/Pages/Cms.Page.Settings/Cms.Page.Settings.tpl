<header class="page-header">
    <h3>{$aCmsPage.Page_Title}</h3>
    <h5>
        <a href="{$aWebsite.url}{$aCmsPage.Page_Url}" target="_blank">{$aWebsite.url}{$aCmsPage.Page_Url}</a>
    </h5>
</header>

<section class="card">
    <div class="card-header">
        <h2 class="card-title">Basis informatie</h2>
        <div class="card-tools">
            <div class="btn-group btn-group-sm">
                {if !$bMobileUser}
                <a class="btn btn-primary" href="/{$aArura.dir}/content/pagina/content?c={$aCmsPage.Page_Id}" target="_blank">
                    <i class="fas fa-pen"></i>
                </a>
                {/if}
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="/{$aArura.dir}/{$aArura.api}/cms/Page.Settings.php" method="post" class="page-settings form-sender">
            <input type="hidden" name="type" value="save-settings">
            <input type="hidden" name="Page_Id" value="{$aCmsPage.Page_Id}">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Naam</label>
                    <input type="text" class="form-control" value="{$aCmsPage.Page_Title}" name="Page_Title">
                </div>
                <div class="col-md-6 form-group">
                    <label>Url</label>
                    <input type="text" class="form-control" value="{$aCmsPage.Page_Url}" name="Page_Url">
                </div>
                <div class="col-12 form-group">
                    <label>Omschrijving</label>
                    <textarea class="form-control" maxlength="1000" name="Page_Description">{$aCmsPage.Page_Description}</textarea>
                </div>
                <div class="form-group col-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="pagepublic" name="Page_Visible" {if $aCmsPage.Page_Visible}checked{/if}>
                        <label class="custom-control-label" for="pagepublic">Pagina openbaar</label>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary btn-sm" type="submit">
                Opslaan
            </button>
        </form>
    </div>
</section>