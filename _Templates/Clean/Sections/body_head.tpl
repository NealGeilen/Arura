<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$aPage.title} | {$aWebsite.name}</title>
    {foreach $aResourceFiles.arura.css as $file}
        <link rel="stylesheet" href="{$file}">
    {/foreach}
    {foreach $aResourceFiles.page.css as $file}
        <link rel="stylesheet" href="{$file}">
    {/foreach}
</head>