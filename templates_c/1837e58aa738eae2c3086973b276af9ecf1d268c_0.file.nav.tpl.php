<?php
/* Smarty version 3.1.36, created on 2020-08-03 09:19:00
  from 'P:\arura\Templates\Sections\nav.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_5f27d68404d712_12216788',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1837e58aa738eae2c3086973b276af9ecf1d268c' => 
    array (
      0 => 'P:\\arura\\Templates\\Sections\\nav.tpl',
      1 => 1579256980,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5f27d68404d712_12216788 (Smarty_Internal_Template $_smarty_tpl) {
?><nav class="navbar navbar-expand-md fixed-top">
    <div class="container-fluid">
        <button class="navbar-toggler navbar-toggler-left btn btn-secondary text-white" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <div class="navbar-brand">
            <a class="navbar-brand-link" href="/">
                <img src="<?php echo $_smarty_tpl->tpl_vars['aWebsite']->value['logo'];?>
">
            </a>
        </div>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav text-center mr-auto">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['aMainNav']->value, 'Item');
$_smarty_tpl->tpl_vars['Item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['Item']->value) {
$_smarty_tpl->tpl_vars['Item']->do_else = false;
?>
            <li class="nav-item<?php if ((isset($_smarty_tpl->tpl_vars['Item']->value['children']))) {?> dropdown<?php }
if ($_smarty_tpl->tpl_vars['Item']->value['url'] === $_SERVER['REDIRECT_URL']) {?> active<?php }?>">
                <a class="nav-link text-capitalize <?php if ((isset($_smarty_tpl->tpl_vars['Item']->value['children']))) {?>dropdown-toggle dropdown-menu-right<?php }?>" href="<?php echo $_smarty_tpl->tpl_vars['Item']->value['url'];?>
" <?php if ((isset($_smarty_tpl->tpl_vars['Item']->value['children']))) {?>role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"<?php }?>>
                <?php echo $_smarty_tpl->tpl_vars['Item']->value['name'];?>

                </a>
                <?php if ((isset($_smarty_tpl->tpl_vars['Item']->value['children']))) {?>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['Item']->value['children'], 'Child');
$_smarty_tpl->tpl_vars['Child']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['Child']->value) {
$_smarty_tpl->tpl_vars['Child']->do_else = false;
?>
                        <?php if ((isset($_smarty_tpl->tpl_vars['Child']->value['children']))) {?>
                            <li class="dropdown-submenu">
                                <a class="dropdown-item dropdown-toggle text-capitalize" href="#"><?php echo $_smarty_tpl->tpl_vars['Child']->value['name'];?>
</a>
                                <ul class="dropdown-menu">
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['Child']->value['children'], 'SubChild');
$_smarty_tpl->tpl_vars['SubChild']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['SubChild']->value) {
$_smarty_tpl->tpl_vars['SubChild']->do_else = false;
?>
                                        <li><a class="dropdown-item text-capitalize" href="<?php echo $_smarty_tpl->tpl_vars['SubChild']->value['url'];?>
"><?php echo $_smarty_tpl->tpl_vars['SubChild']->value['name'];?>
</a></li>
                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                </ul>
                            </li>
                        <?php } else { ?>
                            <li>
                                <a class="dropdown-item text-capitalize" href="<?php echo $_smarty_tpl->tpl_vars['Child']->value['url'];?>
"><?php echo $_smarty_tpl->tpl_vars['Child']->value['name'];?>
</a>
                            </li>
                        <?php }?>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </ul>
                <?php }?>
            </li>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </ul>
        </div>

    </div>
</nav>



<?php }
}
