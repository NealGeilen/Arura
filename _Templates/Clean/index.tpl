<!DOCTYPE html>
<html>
{include "./Sections/body_head.tpl"}
<body class="hold-transition login-page" style="background-image: url('{$aWebsite.banner}')">
{block content}
{/block}
<script>
    ARURA_DIR = "{$aArura.dir}";
    ARURA_API_DIR = "/{$aArura.dir}/{$aArura.api}/";
</script>
{include "./Sections/body_end.tpl"}

</body>
</html>
