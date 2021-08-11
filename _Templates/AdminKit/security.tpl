<!DOCTYPE html>
<html lang="en">

{include "./Sections/head.tpl"}

<body>
<main class="w-100 h-100 security-page background position-relative" style="background-image: url('{$aWebsite.banner}')">
    <div class="security-content">
        {block content}

        {/block}
    </div>

</main>

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