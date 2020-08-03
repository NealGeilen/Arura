<?php
/* Smarty version 3.1.36, created on 2020-08-03 09:19:00
  from 'P:\arura\Templates\Sections\footer.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_5f27d684083f51_06495070',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fab7b813929e397742dbaa225e69bc71f51d47f9' => 
    array (
      0 => 'P:\\arura\\Templates\\Sections\\footer.tpl',
      1 => 1579257072,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5f27d684083f51_06495070 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'P:\\arura\\dashboard\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.date_format.php','function'=>'smarty_modifier_date_format',),));
?>
<footer class="fixed-bottom">
    <div class="container-fluid text-center">
        <div class="row">
            <div class="col-12">
                <span class="text-center"><?php echo $_smarty_tpl->tpl_vars['aWebsite']->value['name'];?>
  © <?php echo smarty_modifier_date_format(time(),"%Y");?>
. Alle rechten voorbehouden.</span>
                <br/>
                <span class="poweredby">Powered and Designed by: <a href="https://www.linkedin.com/in/neal-geilen-43a919194/" target="_blank">Neal Geilen</a></span>
            </div>
        </div>
    </div>
</footer>
<noscript>
    <div class="noscript">
        <div class="alert alert-danger">
            U heeft Javascript niet ingeschakeld voor deze webiste. Hierdoor zal de webiste niet volledig functioneel zijn.
            <hr/>
            Als u deze website wilt gebruiken is het raadzaam javascript in te schakelen. U kunt <a>hier</a> vinden hoe dat moet doen.
        </div>
    </div>
</noscript><?php }
}
