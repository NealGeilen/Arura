<!DOCTYPE html>
<html>
{include "./Sections/body_head.tpl"}
<body class="hold-transition login-page" style="background-image: url('{$aWebsite.banner}')">
{block content}
{/block}
<script>
    ARURA_DIR = "dashboard";
    ARURA_API_DIR = "/dashboard/api/";
</script>
{include "./Sections/body_end.tpl"}
{block jsPage}
{/block}
</body>
</html>
