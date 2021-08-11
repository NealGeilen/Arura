<!DOCTYPE html>
<html lang="nl">

{include "./Sections/head.tpl"}

<body>
<div class="wrapper">
    {include "./Sections/sidebar.tpl"}

    <div class="main">
        {include "./Sections/navbar.tpl"}


        <main class="content">
            <div class="container-fluid p-0">

                <div class="row mb-2 mb-xl-3">
                    <div class="col-auto d-none d-sm-block">
                        <h3>
                            <b>
                                {block title}
                                    {$title}
                                {/block}
                            </b>
                        </h3>
                    </div>

                    <div class="col-auto ms-auto text-end mt-n1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb bg-transparent p-0 mt-1 mb-0">
                                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                                {block breadcrum}
                                {/block}
                            </ol>
                        </nav>
                        <div class="btn-group pageactions">
                            {block pageactions}{/block}
                        </div>
                    </div>
                </div>

                {block content }
                    <h1>Hier is geen content aanwezig :(</h1>
                {/block}

            </div>
        </main>

        {include "./Sections/footer.tpl"}
        {include "./Sections/body_modals.tpl"}
    </div>
</div>
<script>
    var ARURA_DIR = "{$aArura.dir}";
    var ARURA_API_DIR = "/{$aArura.dir}/{$aArura.api}/";
    var WEB_URL = "{$aWebsite.url}";
    var MOBILE_USER = "{$bMobileUser}";
    var FLASHES = '{$Flashes}';
</script>

{include "./Sections/end.tpl"}



</body>

</html>