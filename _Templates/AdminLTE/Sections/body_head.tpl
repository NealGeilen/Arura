<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/files/favicon.ico">
    <title>{$aPage.title} | Dashboard | {$aWebsite.name}</title>
    {foreach from=$aResourceFiles.Css item=File}
    <link href="{$File}" rel="stylesheet">
    {/foreach}
</head>
<style>
    {$aResourceFiles.CssPage}
</style>