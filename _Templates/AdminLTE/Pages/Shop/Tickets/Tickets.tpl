{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/winkel/evenementen">Evenementen</a></li>
    <li class="breadcrumb-item active">{$Event->getName()}</li>
{/block}
{block content}
    {assign var="tabsType" value="tickets"}
    {include file="./../Events/tabs.tpl"}
    <div class="card card-primary">
        <div class="card-body table-responsive">


            <ul class="list-group">
                {foreach $Event->getRegistration() as $Registration}
                    <li class="list-group-item">
                        {$Registration->getFirstname()} {$Registration->getLastname()} | {$Registration->getEmail()}
                        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#Registration-{$Registration->getId()}">
                            Meer
                        </button>
                        <div class="collapse" id="Registration-{$Registration->getId()}">
                            <div class="row">
                                <div class="col-md-3 col-12">
                                    Telefoonnummer: <b>{$Registration->getTel()}</b>
                                </div>
                                {foreach $Fields as $Field}
                                    <div class="col-md-3 col-12">
                                        {$Field->getTitle()}: <b>{$Registration->getAdditionalField($Field->getTag())}</b>
                                    </div>
                                {/foreach}
                                <small>Inschrijf datum: {$Registration->getSignUpTime()|date_format:"%H:%M %d-%m-%Y"}</small>
                            </div>
                        </div>
                    </li>
                {/foreach}
            </ul>

        </div>
    </div>



{/block}