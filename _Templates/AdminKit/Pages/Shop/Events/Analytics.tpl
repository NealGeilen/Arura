{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/winkel/evenementen">Evenementen</a></li>
    <li class="breadcrumb-item active">{$Event->getName()}</li>
{/block}

{block content}
    {assign var="tabsType" value="analytics"}
    {include file="./tabs.tpl"}
    {$Dashboard}
{/block}