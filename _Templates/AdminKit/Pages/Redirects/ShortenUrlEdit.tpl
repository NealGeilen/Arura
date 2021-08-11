{extends "../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/redirects/shorten">Url verkleinen</a></li>
    <li class="breadcrumb-item active">{$Url->getToken()}</li>
{/block}


{block content}
    <div class="card">
        <div class="card-body">
            {$form->startForm()}
            <div class="row">
                <div class="col-12">
                    {$form->getControl("Url_Direction")}
                </div>
            </div>
            {$form->getControl("submit")}
            {$form->endForm()}
        </div>
    </div>
{/block}