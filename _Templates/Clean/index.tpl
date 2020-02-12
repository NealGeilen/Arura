<!DOCTYPE html>
<html>
{include "$TEMPLATEDIR/Sections/body_head.tpl"}
<body class="hold-transition login-page" style="background: url('{$aWebsite.banner}') center no-repeat">
{block content}
{/block}
<script>
    ARURA_DIR = "{$aArura.dir}";
    ARURA_API_DIR = "/{$aArura.dir}/{$aArura.api}/";
</script>
{include "$TEMPLATEDIR/Sections/body_end.tpl"}

</body>
</html>
