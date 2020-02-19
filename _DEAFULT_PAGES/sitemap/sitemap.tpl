<section>
    <h1>Sitemap van {$aWebsite.name}</h1>
    <ul class="list-group">
        {foreach from=$aSitemap item=$link}
            <li class="list-group-item">
                <a href="{$link.loc}">{$link.loc}</a>
                <span class="text-primary">{$link.lastmod.day}-{$link.lastmod.month}-{$link.lastmod.year}</span>
            </li>
        {/foreach}
    </ul>
</section>