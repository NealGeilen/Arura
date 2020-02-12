<!DOCTYPE html>
<html>
{include "$TEMPLATEDIR/Sections/body_start.tpl"}
<body class="hold-transition login-page" style="background: url('{$aWebsite.banner}') center no-repeat">
{block content}
{/block}
{$sContent}
<script>
    ARURA_DIR = "{$aArura.dir}";
    ARURA_API_DIR = "/{$aArura.dir}/{$aArura.api}/";
</script>
{include "$TEMPLATEDIR/Sections/body_end.tpl"}

</body>
</html>
