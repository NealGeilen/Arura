{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/winkel/evenementen">Evenementen</a></li>
    <li class="breadcrumb-item active">{$Event->getName()}</li>
{/block}

{block content}
    {assign var="tabsType" value="form"}
    {include file="./tabs.tpl"}

    <script>
        var _Event_Id = {$Event->getId()}
    </script>


    {if !$Event->hasEventRegistrations() && !$Event->isCanceled()}
        <div class="callout callout-info">
            <p>Aangemaakte velden komen bij het standaard registartie formulier te staan.</p>
            <p>Standaard velden zijn:</p>

            <ul>
                <li>Voornaam</li>
                <li>Achternaam</li>
                <li>Emailadres</li>
                <li>Telefoonnummer</li>
            </ul>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Formulier</h3>
                <div class="card-tools">
                    <div class="btn-group">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#CreatFieldFormModal">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <section class="editor row">
                    {foreach $Fields as $Field}
                        {$Field->render(true)}
                    {/foreach}
                </section>
            </div>
        </div>
    {/if}
    {if $Event->isCanceled() && !$Event->hasEventRegistrations()}
        <div class="callout callout-info">
            Formulier kan niet aangepast worden wanneer het geannuleerd is.
        </div>
    {/if}
    {if $Event->hasEventRegistrations()}
        <div class="callout callout-info">
            Formulier kan niet aangepast worden wanneer er aanmeldingen zijn.
        </div>
    {/if}

    <div class="template-field-block d-none">

    </div>

    <!-- Modal -->
    <div class="modal fade" id="CreatFieldFormModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Veld toevoegen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {$CreatFieldForm}
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="EditFieldFormModal" >
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Veld aanpassen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
{/block}