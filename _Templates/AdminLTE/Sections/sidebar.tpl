<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/dashboard" class="brand-link">
        <img src="{$aWebsite.logo}" class="brand-image">
        <span class="brand-text font-weight-light">{$aWebsite.name}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                {foreach from=$aNavPages key=Href item=aPage}
                {if $aPage.Right && $aPage.Icon != Null}
                <li class="nav-item {if isset($aPage.Children)}has-treeview{if in_array($sRequestUrl,array_keys($aPage.Children)) || (isset($aPage.Open) && $aPage.Open)} menu-open{/if}{/if}">
                    <a href="/{$aArura.dir}{$Href}" class="nav-link {if $Href === $sRequestUrl}active{/if}">
                        <i class="nav-icon {$aPage.Icon}"></i>
                        <p>
                            {$aPage.Title}
                            {if isset($aPage.Children)}
                            <i class="right fas fa-angle-left"></i>
                            {/if}
                        </p>
                    </a>
                    {if isset($aPage.Children)}
                    <ul class="nav nav-treeview">
                        {foreach from=$aPage.Children item=Child key=ChildHref}
                            {if $Child.Right && $Child.Icon != NULL}
                                <li class="nav-item {if isset($Child.Children)}has-treeview{if in_array($sRequestUrl,array_keys($Child.Children))} menu-open{/if}{/if}">
                                    <a href="/{$aArura.dir}{$ChildHref}" class="nav-link{if $ChildHref === $sRequestUrl} active{/if}">
                                        <i class="{$Child.Icon} nav-icon"></i>
                                        <p>
                                            {$Child.Title}
                                            {if isset($Child.Children)}
                                            <i class="right fas fa-angle-left"></i>
                                            {/if}
                                        </p>
                                    </a>
                                    {if isset($Child.Children)}
                                    <ul class="nav nav-treeview">
                                        {foreach from=$Child.Children item=Grand key=GrandHref}
                                            {if $Grand.Right && $Grand.Icon != NULL}
                                            <li class="nav-item">
                                                <a href="/{$aArura.dir}{$GrandHref}" class="nav-link{if $GrandHref === $sRequestUrl} active{/if}">
                                                    <i class="{$Grand.Icon} nav-icon"></i>
                                                    <p>{$Grand.Title}</p>
                                                </a>
                                            </li>
                                            {/if}
                                        {/foreach}
                                    </ul>
                                    {/if}
                                </li>
                            {/if}
                        {/foreach}
                    </ul>
                    {/if}
                </li>
                {/if}
                {/foreach}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>