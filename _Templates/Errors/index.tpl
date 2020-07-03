<!DOCTYPE html>
<html lang="nl">
{include "./Sections/body_head.tpl"}
<body class="login-page" style="height: 100vh; background-image: url('{$aWebsite.banner}')">
<div class="container">

    <section class="bg-primary p-4 text-center" style="margin-top: 30vh">
        <i class="far fa-grin-beam-sweat fa-4x"></i>
        <h1>Oeps.... </h1>
        <h2>Het lijkt er op dat er iets is misgegeaan</h2>
        {if $debug}

            <table class="table table-light text-left text-dark">
                <tbody>
                <tr>
                    <th>Bericht:</th>
                    <td>{$exception->getMessage()}</td>
                </tr>
                <tr>
                    <th>Code:</th>
                    <td>{$exception->getCode()}</td>
                </tr>
                <tr>
                    <th>File:</th>
                    <td>{$exception->getFile()}</td>
                </tr>
                <tr>
                    <th>Regel:</th>
                    <td>{$exception->getLine()}</td>
                </tr>
                <tr>
                    <th>Trace:</th>
                    <td>
                        <ul class="list-unstyled">
                            {foreach $exception->getTrace() as $trace}
                                <li class="bg-secondary m-4 p-2 rounded">
                                    {foreach $trace as $key => $value}
                                        {if $key !== "args"}
                                            <p><b class="text-capitalize">{$key}:</b> {$value}</p>
                                        {/if}
                                    {/foreach}
                                </li>
                            {/foreach}
                        </ul>
                    </td>
                </tr>
                </tbody>
            </table>
        {/if}
        <p>Klik <a href="/dashboard" class="text-white" style="text-decoration: underline">hier</a> om weer terug te gaan</p>
    </section>

</div>
<script>
    ARURA_DIR = "dashboard";
    ARURA_API_DIR = "/dashboard/api/";
</script>
{include "./Sections/body_end.tpl"}
{block jsPage}
{/block}
</body>
</html>
