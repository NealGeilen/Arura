{extends "../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item active">Bestanden</li>
{/block}

{block content}
    <div class="card">
        <div class="card-body">
            <iframe src="/dashboard/files/frame" class="border-0 w-100" style="min-height: 600px"></iframe>
        </div>
    </div>

{/block}