{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/gallery">Albums</a></li>
    <li class="breadcrumb-item"><a href="/dashboard/gallery/{$Gallery->getId()}">{$Gallery->getName()}</a></li>
    <li class="breadcrumb-item active">Instellingen</li>
{/block}

{block content}
    <div class="card card-primary">
        <div class="card-header">
            <h2 class="card-title">Instellingen</h2>
            <div class="card-tools">
                <a class="btn btn-primary" href="/dashboard/gallery/{$Gallery->getId()}">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
            {$editForm}
        </div>
    </div>

{/block}