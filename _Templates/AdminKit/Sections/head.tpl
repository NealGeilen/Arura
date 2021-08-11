<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{$aWebsite.favicon}">
    <title>{$title} | Dashboard | {$aWebsite.name}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    {foreach $aResourceFiles.arura.css as $file}
        <link rel="stylesheet" href="{$file}">
    {/foreach}
    {if isset($aResourceFiles.page.css)}
        {foreach $aResourceFiles.page.css as $file}
            <link rel="stylesheet" href="{$file}">
        {/foreach}
    {/if}
</head>