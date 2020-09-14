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
                        <button class="btn btn-primary" onclick="">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <section class="editor row">
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
        <div class="Field-Item">
            <div class="Field-Item-Control">
                <div class="btn-group-vertical btn-group-sm">
                    <span class="btn btn-xsm btn-primary Field-Position-Handler">
                        <i class="fas fa-arrows-alt"></i>
                    </span>
                    <button class="btn btn-xsm btn-primary">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button class="btn btn-xsm btn-primary" onclick="Builder.Block.Delete($(this).parents('.Block-Item'))">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
            <div class="Field-Item-Content">
            </div>
            <span class="btn btn-sm btn-primary Field-Item-Width-Control ui-resizable-handle ui-resizable-e">
                        <i class="fas fa-arrows-alt-h"></i>
            </span>
        </div>
    </div>


    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
        Launch demo modal
    </button>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Formulier veld</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-row">
                            <div class="col-6 form-group">
                                <label>Naam</label>
                                <input class="form-control" type="text" name="Field_Title" required>
                            </div>
                            <div class="col-6 form-group">
                                <label>Tag</label>
                                <input class="form-control" type="text" name="Field_Tag" required>
                            </div>
                            <div class="col-6 form-group">
                                <label>Soort veld</label>
                                <select class="form-control" name="Field_Type">
                                    <option value="text">Text</option>
                                    <option value="number">Nummer</option>
                                    <option value="email">Email</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
{/block}