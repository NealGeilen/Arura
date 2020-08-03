<?php
/* Smarty version 3.1.36, created on 2020-08-03 09:19:00
  from 'P:\arura\Templates\Sections\body_end.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_5f27d6840ca7a3_83610876',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2255da333c2523cb2b9dc4b8082f1b26c47e6267' => 
    array (
      0 => 'P:\\arura\\Templates\\Sections\\body_end.tpl',
      1 => 1593263517,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5f27d6840ca7a3_83610876 (Smarty_Internal_Template $_smarty_tpl) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['aResourceFiles']->value['js'], 'file');
$_smarty_tpl->tpl_vars['file']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['file']->value) {
$_smarty_tpl->tpl_vars['file']->do_else = false;
?>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['file']->value;?>
"><?php echo '</script'; ?>
>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
echo '<script'; ?>
 async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $_smarty_tpl->tpl_vars['app']->value["analytics google"]['Tag'];?>
"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
>
    <?php if ($_smarty_tpl->tpl_vars['app']->value["analytics google"]['Tag']) {?>
    Arura.Cookies.init();
    
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    
    gtag('js', new Date());
    gtag('config', '<?php echo $_smarty_tpl->tpl_vars['app']->value["analytics google"]['Tag'];?>
');
    <?php }
echo '</script'; ?>
><?php }
}
