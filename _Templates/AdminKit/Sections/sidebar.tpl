<nav id="sidebar" class="sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="/dashboard">
            <img src="{$aWebsite.logo}" style="max-width: 100%; max-height: 100px; margin: 0 auto; display: block">
        </a>
        <div class="text-white text-center w-100" style="font-size: 24px">
            {$aWebsite.name}
        </div>

        <ul class="sidebar-nav">
            <li class="sidebar-item">
                <a class="sidebar-link" href="/dashboard/home">
                    <i class="fas fa-home"></i><span class="align-middle">Dashboard</span>
                </a>
            </li>
            {if $Permissions.CMS_PAGES || $Permissions.CMS_ADDONS || $Permissions.CMS_MENU }
                <li class="sidebar-item">
                    <a data-bs-target="#side-content-tab" data-bs-toggle="collapse" class="sidebar-link collapsed">
                        <i class="fas fa-columns"></i><span>Content</span>
                    </a>
                    <ul id="side-content-tab" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        {if $Permissions.CMS_PAGES}
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="/dashboard/content/paginas">Pagina's</a>
                            </li>
                        {/if}
                        {if $Permissions.CMS_ADDONS}
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="/dashboard/content/addons">Addons</a>
                            </li>
                        {/if}
                        {if $Permissions.CMS_MENU}
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="/dashboard/content/menu">Menu</a>
                            </li>
                        {/if}
                    </ul>
                </li>
            {/if}
            {if $Permissions.FILES_READ && $Permissions.FILES_EDIT && $Permissions.FILES_UPLOAD}
                <li class="sidebar-item">
                    <a class="sidebar-link" href="/dashboard/files">
                        <i class="fas fa-folder"></i><span class="align-middle">Bestanden</span>
                    </a>
                </li>
            {/if}
            {if $Permissions.GALLERY_MANGER}
                <li class="sidebar-item">
                    <a class="sidebar-link" href="/dashboard/gallery">
                        <i class="fas fa-images"></i><span class="align-middle">Album's</span>
                    </a>
                </li>
            {/if}
            {if $Permissions.SHOP_PAYMENTS}
                <li class="sidebar-item">
                    <a class="sidebar-link" href="/dashboard/winkel/betalingen">
                        <i class="fas fa-money-bill-wave-alt"></i><span class="align-middle">Betaling</span>
                    </a>
                </li>
            {/if}
            {if $Permissions.SHOP_EVENTS_MANAGEMENT && $Permissions.SHOP_EVENTS_REGISTRATION}
                <li class="sidebar-item">
                    <a class="sidebar-link" href="/dashboard/winkel/evenementen">
                        <i class="far fa-calendar-alt"></i><span class="align-middle">Evenementen</span>
                    </a>
                </li>
            {/if}
            {if $Permissions.ARURA_USERS || $Permissions.ARURA_SETTINGS || $Permissions.ARURA_UPDATER || $Permissions.ARURA_LOGS || $Permissions.ARURA_WEBHOOK }
            <li class="sidebar-item">
                <a data-bs-target="#side-arura-tab" data-bs-toggle="collapse" class="sidebar-link collapsed">
                    <i class="fas fa-toolbox"></i><span>Arura</span>
                </a>
                <ul id="side-arura-tab" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    {if $Permissions.ARURA_USERS}
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/dashboard/arura/users">Gebruikers</a>
                        </li>
                    {/if}
                    {if $Permissions.ARURA_SETTINGS}
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/dashboard/arura/settings">Instellingen</a>
                        </li>
                    {/if}
                    {if $Permissions.ARURA_WEBHOOK}
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/dashboard/arura/webhook">Webhook</a>
                        </li>
                    {/if}
                    {if $Permissions.ARURA_UPDATER}
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/dashboard/arura/updater?t=data">Updaten</a>
                        </li>
                    {/if}
                    {if $Permissions.ARURA_LOGS}
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/dashboard/arura/logs">Logs</a>
                        </li>
                    {/if}
                </ul>
            </li>
            {/if}
        </ul>
    </div>
</nav>