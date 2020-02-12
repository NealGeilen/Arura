<!DOCTYPE html>
<html>
{include "$TEMPLATEDIR/Sections/body_head.tpl"}
<body class="hold-transition sidebar-mini-md layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  {include "$TEMPLATEDIR/Sections/navbar.tpl"}
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  {include "$TEMPLATEDIR/Sections/sidebar.tpl"}

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
      {block content}
      {/block}
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
  {include "$TEMPLATEDIR/Sections/footer.tpl"}
  {include "$TEMPLATEDIR/Sections/body_modals.tpl"}


  {block sidebar}
  {/block}
</div>
<!-- ./wrapper -->

{include "$TEMPLATEDIR/Sections/body_end.tpl"}
</body>
</html>
