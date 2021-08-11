{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/content/addons">Addons</a></li>
    <li class="breadcrumb-item active">Nieuw</li>
{/block}

{block content}
    <div class="card card-primary">
        <header class="card-header">
            <h2 class="card-title">Nieuw</h2>
        </header>
        <div class="card-body" style="display: block;">
            {$Form}
        </div>
    </div>
{/block}

{block JsPage}
    <script>
    </script>
{/block}