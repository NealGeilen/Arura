<?php
/* Smarty version 3.1.36, created on 2020-08-03 09:18:59
  from 'P:\arura\Templates\Sections\body_head.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_5f27d683f1ea24_71128749',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '50e87596c8e9d08a66206974c703973545151b09' => 
    array (
      0 => 'P:\\arura\\Templates\\Sections\\body_head.tpl',
      1 => 1594034341,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5f27d683f1ea24_71128749 (Smarty_Internal_Template $_smarty_tpl) {
?><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="<?php echo $_smarty_tpl->tpl_vars['sPageDescription']->value;?>
">
<link rel="icon" href="<?php echo $_smarty_tpl->tpl_vars['aWebsite']->value['favicon'];?>
">

<title><?php echo $_smarty_tpl->tpl_vars['sPageTitle']->value;?>
 | <?php echo $_smarty_tpl->tpl_vars['aWebsite']->value['name'];?>
</title>
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['aResourceFiles']->value['css'], 'file');
$_smarty_tpl->tpl_vars['file']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['file']->value) {
$_smarty_tpl->tpl_vars['file']->do_else = false;
?>
    <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['file']->value;?>
">
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
