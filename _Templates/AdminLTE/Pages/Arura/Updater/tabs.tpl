<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link{if $tabsType=== "data"} active{/if}" href="?t=data" role="tab">Database</a>
    </li>
    <li class="nav-item">
        <a class="nav-link{if $tabsType=== "git"} active{/if}" href="?t=git" role="tab">Git</a>
    </li>
    <li class="nav-item">
        <a class="nav-link{if $tabsType=== "package"} active{/if}" href="?t=package" role="tab">Composer</a>
    </li>
</ul>