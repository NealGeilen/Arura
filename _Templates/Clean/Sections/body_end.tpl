{foreach $aResourceFiles.arura.js as $file}
    <script src="{$file}"></script>
{/foreach}
{if isset($aResourceFiles.page.js)}
    {foreach $aResourceFiles.page.js as $file}
        <script src="{$file}"></script>
    {/foreach}
{/if}