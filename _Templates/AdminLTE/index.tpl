<!DOCTYPE html>
<html>
{include "./Sections/body_head.tpl"}
<body class="hold-transition layout-fixed{if $sPageSideBar != NULL} control-sidebar-push-slide control-sidebar-open{/if}">
<div class="wrapper">

  <!-- Navbar -->
  {include "./Sections/navbar.tpl"}
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  {include "./Sections/sidebar.tpl"}

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container">
        {block contentHeader}
        {/block}
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container">
        {block content}
        {/block}
      </div>
    </section>

    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <script>
    var ARURA_DIR = "{$aArura.dir}";
    var ARURA_API_DIR = "/{$aArura.dir}/{$aArura.api}/";
    var WEB_URL = "{$aWebsite.url}";
    var MOBILE_USER = "{$bMobileUser}";
    var FLASHES = '{$Flashes}';
  </script>
  {include "./Sections/footer.tpl"}
  {include "./Sections/body_modals.tpl"}


  {if $sPageSideBar != NULL}
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      {$sPageSideBar}
    </aside>
    <!-- /.control-sidebar -->
  {/if}

</div>
<!-- ./wrapper -->

{include "./Sections/body_end.tpl"}
{block JsPage}
{/block}
</body>
</html>
