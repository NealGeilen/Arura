<!DOCTYPE html>
<html>
{$body_head}
<body class="hold-transition sidebar-mini-md layout-fixed{if $sPageSideBar != NULL} control-sidebar-push-slide control-sidebar-open{/if}">
<div class="wrapper">

  <!-- Navbar -->
  {$navbar}
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  {$sidebar}

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
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
      {$sContent}
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
  {$footer}
  {$body_modals}

  {if $sPageSideBar != NULL}
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    {$sPageSideBar}
  </aside>
  <!-- /.control-sidebar -->
  {/if}
</div>
<!-- ./wrapper -->

{$body_end}
</body>
</html>