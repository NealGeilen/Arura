<nav aria-label="Page navigation">
    <ul class="pagination">
        <li class="page-item{if $iCurrentPage == 1} disabled{/if}">
            <a class="page-link" href="?p={$iCurrentPage-1}&q={if isset($smarty.get.q)}{$smarty.get.q}{/if}" aria-label="Previous">
                <i class="fas fa-arrow-left"></i>
            </a>
        </li>
        {for $foo=1 to $iAmountPages}
            <li class="page-item{if $iCurrentPage == $foo} active{/if}">
                <a class="page-link" href="?p={$foo}&q={if isset($smarty.get.q)}{$smarty.get.q}{/if}">{$foo}</a>
            </li>
        {forelse}
        {/for}
        <li class="page-item {if $iCurrentPage == $iAmountPages} disabled{/if}">
            <a class="page-link" href="?p={$iCurrentPage+1}&q={if isset($smarty.get.q)}{$smarty.get.q}{/if}" aria-label="Next">
                <i class="fas fa-arrow-right"></i>
            </a>
        </li>
    </ul>
</nav>
<small>Pagina {$iCurrentPage} van de {$iAmountPages}</small>