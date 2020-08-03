<?php
/* Smarty version 3.1.36, created on 2020-08-03 09:18:59
  from 'P:\arura\Templates\index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_5f27d683e7db77_32642571',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9c39f3ec77f9e04bde202232738b3f039a14670e' => 
    array (
      0 => 'P:\\arura\\Templates\\index.tpl',
      1 => 1581690430,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:./Sections/body_head.tpl' => 1,
    'file:./Sections/nav.tpl' => 1,
    'file:./Sections/footer.tpl' => 1,
    'file:./Sections/body_end.tpl' => 1,
  ),
),false)) {
function content_5f27d683e7db77_32642571 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="en">

<head>
    <?php $_smarty_tpl->_subTemplateRender("file:./Sections/body_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
</head>


<body id="page-top">

<?php $_smarty_tpl->_subTemplateRender("file:./Sections/nav.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    <div class="content-container container">
        <?php if (is_array($_smarty_tpl->tpl_vars['content']->value)) {?>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['content']->value, 'aGroup');
$_smarty_tpl->tpl_vars['aGroup']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['aGroup']->value) {
$_smarty_tpl->tpl_vars['aGroup']->do_else = false;
?>
                <section class="Group <?php echo $_smarty_tpl->tpl_vars['aGroup']->value['Group_Css_Class'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['aGroup']->value['Group_Css_Id'];?>
">
                    <div class="container">
                        <div class="row">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['aGroup']->value['Content_Blocks'], 'aBlock');
$_smarty_tpl->tpl_vars['aBlock']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['aBlock']->value) {
$_smarty_tpl->tpl_vars['aBlock']->do_else = false;
?>
                            <div class="Block col-md-<?php echo $_smarty_tpl->tpl_vars['aBlock']->value['Content_Size'];?>
" style="<?php if (!empty($_smarty_tpl->tpl_vars['aBlock']->value['Content_Css_Background_Color'])) {?>background-color: <?php echo $_smarty_tpl->tpl_vars['aBlock']->value['Content_Css_Background_Color'];?>
;<?php }
if (!empty($_smarty_tpl->tpl_vars['aBlock']->value['Content_Css_Background_Img'])) {?>background-image: url('<?php echo $_smarty_tpl->tpl_vars['aBlock']->value['Content_Css_Background_Img'];?>
');<?php }?>">
                                <?php echo $_smarty_tpl->tpl_vars['aBlock']->value['Template'];?>

                            </div>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </div>
                    </div>
                </section>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        <?php } else { ?>
            <?php echo $_smarty_tpl->tpl_vars['content']->value;?>

        <?php }?>
    </div>

<?php $_smarty_tpl->_subTemplateRender("file:./Sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

</body>

<?php $_smarty_tpl->_subTemplateRender("file:./Sections/body_end.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

</html>


<?php }
}
