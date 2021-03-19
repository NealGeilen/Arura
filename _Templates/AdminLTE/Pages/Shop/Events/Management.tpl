{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Evenementen</li>
{/block}

{block content}

    <div class="card card-primary">
        <div class="card-header">
            <h2 class="card-title">Aankomende evenementen</h2>
        </div>
        <div class="card-body">
            <div class="row">
                {foreach $UpcomingEvents as $Event}
                    <div class="col-md-4 col-12">
                        <div class="card card-body">
                            <h4>{$Event->getName()} <span class="badge badge-info">{$Event->getStatus()}</span></h4>
                            <small>
                                {$Event->getStart()|date_format:"%H:%M %d-%m-%y"} t/m {$Event->getEnd()|date_format:"%H:%M %d-%m-%y"}
                            </small>
                            <a href="/event/{$Event->getSlug()}" target="_blank">{$Event->getName()}</a>

                            <a class="btn btn-primary btn-sm" href="/{$aArura.dir}/winkel/evenement/{$Event->getId()}">
                                Meer
                            </a>
                        </div>
                    </div>
                {/foreach}
                {if $aPermissions.SHOP_EVENTS_MANAGEMENT}
                    <div class="col-md-4 col-12">
                        <div class="card card-body bg-primary">
                            <h4>Nieuw evenement</h4>
                            <small>
                                Nieuw evenement aanmaken
                            </small>
                            <a class="btn btn-secondary" href="/dashboard/winkel/evenementen/aanmaken"><i class="fas fa-plus"></i></a>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </div>


    <div class="card collapsed-card">
        <div class="card-header">
            <h2 class="card-title">Afgeronden evenementen</h2>
            <div class="card-tools">
                <div class="btn-group">
                    <button class="btn btn-secondary" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
            </div>
        </div>

        <div class="card-body" style="display: none;">
            <div class="row">
                {foreach $Events as $Event}
                    <div class="col-md-4 col-12">
                        <div class="card card-body">
                            <h4>{$Event->getName()} <span class="badge badge-info">{$Event->getStatus()}</span></h4>
                            <small>
                                {$Event->getStart()|date_format:"%H:%M %d-%m-%y"} t/m {$Event->getEnd()|date_format:"%H:%M %d-%m-%y"}
                            </small>
                            <a href="/event/{$Event->getSlug()}" target="_blank">{$Event->getName()}</a>

                            <a class="btn btn-primary btn-sm" href="/{$aArura.dir}/winkel/evenement/{$Event->getId()}">
                                Meer
                            </a>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
{/block}