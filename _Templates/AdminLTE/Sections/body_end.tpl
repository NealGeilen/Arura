{foreach from=$aResourceFiles.Js item=File}
<script src="{$File}?{$smarty.now}"></script>
{/foreach}
<script>
    {$aResourceFiles.JsPage}
    setInterval(validateUser, 5000);
    loadChartJsPhp();
</script>