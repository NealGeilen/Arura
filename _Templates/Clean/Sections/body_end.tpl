{foreach from=$aResourceFiles.Js item=File}
<script src="{$File}"></script>
{/foreach}
<script>
    {$aResourceFiles.JsPage}

</script>