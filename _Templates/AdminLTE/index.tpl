<!DOCTYPE html>
<html>
{include "./Sections/body_head.tpl"}
<body class="hold-transition sidebar-mini-md layout-fixed{if $sPageSideBar != NULL} control-sidebar-push-slide control-sidebar-open{/if}">
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
      <div class="container-fluid">
        <div class="flashes">
          {$Flashes}
        </div>
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">{$aPage.title}</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
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
    ARURA_DIR = "{$aArura.dir}";
    ARURA_API_DIR = "/{$aArura.dir}/{$aArura.api}/";
    WEB_URL = "{$aWebsite.url}";
    MOBILE_USER = "{$bMobileUser}";
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
